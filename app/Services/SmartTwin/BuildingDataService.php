<?php

namespace App\Services\SmartTwin;

use App\Exceptions\AddressNotSupportedBySmartTwin;
use App\Helpers\Hoomdossier;
use App\Models\Building;
use App\Models\BuildingType;
use App\Models\BuildingTypeCategory;
use App\Models\EnergyLabel;
use App\Models\InputSource;
use App\Models\ToolQuestion;
use App\Services\SmartTwin\Api\SmartTwinApi;
use App\Services\ToolQuestionService;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

/**
 * The basic data of a dwelling from SmartTwin's Building Data API: building type, build year,
 * surface and registered energy label. In a cooperation with SmartTwin these come from SmartTwin
 * rather than from BAG and EP-Online, and SmartTwin's answer wins over what was there (decision
 * 2026-10-15-smarttwin-woninggegevens-building-data, B3 and B9).
 *
 * Written through ToolQuestionService in the input source it is fetched for, which copies it to
 * master, like every SmartTwin import.
 */
class BuildingDataService
{
    /** SmartTwin's BuildingType to the short of the building type category. 0 (Unknown) is not here. */
    private const CATEGORIES = [
        1 => 'detached-house',
        2 => '2-homes-under-1-roof',
        3 => 'corner-house',
        4 => 'terraced-house',
        5 => 'apartment',
    ];

    public function __construct(private readonly SmartTwinApi $api, private readonly DwellingRows $dwellingRows)
    {
    }

    /**
     * Fetches the basic data of the building's address and writes it for the input source.
     */
    public function refresh(Building $building, InputSource $inputSource): void
    {
        $data = $this->fetch($building);

        if (is_null($data)) {
            Log::warning('SmartTwin has no building data for the address', [
                'building_id' => $building->id,
                'postal_code' => $building->postal_code,
                'number'      => $building->number,
                'extension'   => $building->extension,
            ]);

            return;
        }

        if (($data['isSupported'] ?? null) === false) {
            report(new AddressNotSupportedBySmartTwin($building, Hoomdossier::cooperationOf($building)));
        }

        $this->apply($building, $inputSource, $data);
    }

    /**
     * The basic data of the building's address, or null when SmartTwin does not know the address.
     *
     * Hoomdossier keeps the house letter and the addition in one field, SmartTwin wants them apart,
     * and "C1" can be either an addition or letter C with addition 1. So the address is tried the
     * ways it can be read, like BagService does: as an addition, as a house letter, and as a letter
     * followed by an addition. A 404 means "not this way"; any other answer is the answer.
     *
     * @return array<string, mixed>|null
     */
    public function fetch(Building $building): ?array
    {
        foreach ($this->addressesFor($building) as $address) {
            try {
                return $this->api->buildingData()->basicDataByAddress($address);
            } catch (ClientException $e) {
                if ($e->getResponse()->getStatusCode() !== 404) {
                    throw $e;
                }
            }
        }

        return null;
    }

    /**
     * The ways to send the building's address, most likely first.
     *
     * @return array<int, array<string, string|int>>
     */
    public function addressesFor(Building $building): array
    {
        $address = [
            'postal_code'  => strtoupper((string) preg_replace('/\s+/', '', (string) $building->postal_code)),
            'house_number' => (int) $building->number,
        ];

        // Letters and digits only: "A-3", "a 3" and "A3" are one extension.
        $extension = strtoupper((string) preg_replace('/[^a-z0-9]/i', '', (string) $building->extension));

        if ($extension === '') {
            return [$address];
        }

        $addresses = [];

        if (preg_match('/^[A-Z0-9]{1,4}$/', $extension)) {
            $addresses[] = $address + ['house_number_addition' => $extension];
        }

        if (preg_match('/^[A-Z]$/', $extension)) {
            $addresses[] = $address + ['house_letter' => $extension];
        }

        if (preg_match('/^([A-Z])([A-Z0-9]{1,4})$/', $extension, $parts)) {
            $addresses[] = $address + ['house_letter' => $parts[1], 'house_number_addition' => $parts[2]];
        }

        return $addresses;
    }

    /**
     * Writes what SmartTwin knows, leaving alone what it does not (null) or what the question would
     * not accept (a build year of 0, a surface under 20 m²).
     *
     * @param  array<string, mixed>  $data  A BasicDataByAddressResponseModel.
     */
    public function apply(Building $building, InputSource $inputSource, array $data): void
    {
        DB::transaction(function () use ($building, $inputSource, $data) {
            // As for an import: the input source's row is what master becomes (see DwellingRows).
            $this->dwellingRows->equalizeToMaster($building, $inputSource);

            $category = BuildingTypeCategory::where('short', self::CATEGORIES[(int) ($data['buildingType'] ?? 0)] ?? null)->first();

            if ($category instanceof BuildingTypeCategory) {
                // Read before writing: the write copies the input source's row to master.
                $type = $this->typeWithin($building, $category);

                // A category with a single type sets that type as well (QuestionAnswers\BuildingTypeCategory).
                $this->save($building, $inputSource, 'building-type-category', $category->id);

                if (BuildingType::where('building_type_category_id', $category->id)->count() > 1) {
                    $this->save($building, $inputSource, 'building-type', $type?->id, validate: false);
                }
            }

            $this->save($building, $inputSource, 'build-year', $data['constructionYear'] ?? null);
            $this->save($building, $inputSource, 'surface', $data['livingSpaceArea'] ?? null);

            if (! empty($data['registeredEnergyLabel'])) {
                $this->save($building, $inputSource, 'energy-label', EnergyLabel::fromName($data['registeredEnergyLabel'])->id);
            }
        });
    }

    /**
     * The type the dwelling has now, if it belongs to the category SmartTwin gives.
     *
     * SmartTwin knows an apartment, not which of the six kinds Hoomdossier has. A type that is one of
     * them stays: someone chose it, and it does not contradict SmartTwin. Any other type is left
     * over from a category the dwelling no longer has, and goes (B14). Choosing Appartement does not
     * clear the type by itself (QuestionAnswers\BuildingTypeCategory only sets a type for a category
     * with one), hence the explicit write.
     */
    private function typeWithin(Building $building, BuildingTypeCategory $category): ?BuildingType
    {
        $type = BuildingType::find($building->getAnswer(InputSource::master(), ToolQuestion::findByShort('building-type')));

        return $type instanceof BuildingType && (int) $type->building_type_category_id === (int) $category->id
            ? $type
            : null;
    }

    private function save(Building $building, InputSource $inputSource, string $short, mixed $value, bool $validate = true): void
    {
        $toolQuestion = ToolQuestion::findByShort($short);

        if ($validate && (is_null($value) || Validator::make(['value' => $value], ['value' => $toolQuestion->validation ?? []])->fails())) {
            return;
        }

        ToolQuestionService::init()
            ->toolQuestion($toolQuestion)
            ->building($building)
            ->currentInputSource($inputSource)
            ->save($value);
    }
}
