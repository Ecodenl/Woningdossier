<?php

namespace App\Console\Commands\Api\SmartTwin;

use App\Enums\MappingType;
use App\Helpers\Hoomdossier;
use App\Models\MeasureApplication;
use App\Services\MappingService;
use App\Services\SmartTwin\Api\SmartTwinApi;
use App\Services\SmartTwin\Mapping\Mappers\SolutionMeasures;
use Illuminate\Console\Command;

/**
 * Couples SmartTwin's catalogue of solutions to our measure applications.
 *
 * Reads the whole catalogue rather than waiting for a product to turn up unmapped in somebody's
 * advice, and couples on the kind of measure rather than the individual product: one row covers
 * every EPS, wool and foam variant of cavity insulation, including the ones SmartTwin adds later.
 * A product that turns out to need a measure of its own can still be given a row on its full id,
 * which SolutionMeasures prefers over the general one.
 *
 * Run it with --dry-run first. That prints the catalogue grouped by kind and says which kinds have
 * no coupling yet, which is the list of decisions still to make.
 */
class SyncSolutions extends Command
{
    protected $signature = 'api:smarttwin:sync-solutions {--dry-run}';

    protected $description = 'Couple the SmartTwin solution catalogue to our measure applications.';

    /**
     * The kind of measure, as it appears before the provider in a solution id, to our measure
     * application.
     *
     * PROVISIONAL. These are read off the five products that appeared in one real coach dossier and
     * have not been checked against the catalogue or confirmed by anyone who knows the measures.
     * Two in particular are a coin flip until someone says otherwise:
     *
     *   - "Replace Glass" could be hrpp-glass-only or hrpp-glass-frames; the name says glass only.
     *   - The roof entries could be the "current" or the "replace-current" variants.
     *
     * A kind that is not in here is reported, not guessed at.
     */
    private const KINDS = [
        'Insulate Facade Cavity'       => 'cavity-wall-insulation',
        'Replace Glass'                => 'hrpp-glass-only',
        'Insulate Flat Roof Outside'   => 'roof-insulation-flat-current',
        'Insulate Pitched Roof Inside' => 'roof-insulation-pitched-inside',
        'Install Ventilation'          => 'ventilation-decentral-wtw',
    ];

    public function handle(SmartTwinApi $api, SolutionMeasures $solutionMeasures): int
    {
        if (! Hoomdossier::hasEnabledSmartTwinCalls()) {
            $this->error('SmartTwin calls are disabled, set SMARTTWIN_ENABLED and SMARTTWIN_KEY.');

            return self::FAILURE;
        }

        $dryRun = (bool) $this->option('dry-run');

        if ($dryRun) {
            $this->info('DRY-RUN mode: no changes will be made.');
        }

        $solutions = $api->advice()->getAllSolutions()['solutions'] ?? [];

        if (empty($solutions)) {
            $this->error('The catalogue came back empty, aborting without changing anything.');

            return self::FAILURE;
        }

        $this->info(count($solutions) . ' solutions in the catalogue.');

        // Grouped so the output reads as the decision list it is: one line per kind, however many
        // products hang off it.
        $products = [];

        foreach ($solutions as $solution) {
            $kind = $solutionMeasures->kindOf($solution['id'] ?? '') ?? '(geen provider in het id)';
            $products[$kind][] = $solution['name'] ?? $solution['id'] ?? '?';
        }

        ksort($products);

        $coupled = 0;
        $unknown = [];

        foreach ($products as $kind => $names) {
            $measureShort = self::KINDS[$kind] ?? null;

            if (is_null($measureShort)) {
                $unknown[$kind] = $names;
                $this->warn(sprintf('%-34s geen koppeling (%d product(en))', $kind, count($names)));

                foreach ($names as $name) {
                    $this->line("    {$name}");
                }

                continue;
            }

            $measure = MeasureApplication::findByShort($measureShort);

            if (! $measure instanceof MeasureApplication) {
                $this->error("Measure application '{$measureShort}' not found, aborting without changing anything.");

                return self::FAILURE;
            }

            $this->line(sprintf('%-34s -> %-38s (%d product(en))', $kind, $measureShort, count($names)));

            if (! $dryRun) {
                MappingService::init()
                    ->from($kind)
                    ->sync([$measure], MappingType::SMARTTWIN_SOLUTION_MEASURE_APPLICATION->value);
            }

            ++$coupled;
        }

        $this->newLine();
        $this->info(sprintf(
            '%d kind(s) %s, %d without a coupling.',
            $coupled,
            $dryRun ? 'would be coupled' : 'coupled',
            count($unknown),
        ));

        if (! empty($unknown)) {
            $this->warn('A solution of an uncoupled kind reaches no woonplan; it is reported as VALUE_UNMAPPED.');
        }

        return self::SUCCESS;
    }
}
