<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\GetMyValuesTrait;
use App\Traits\GetValueTrait;
use App\Traits\ToolSettingTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\UserInterest
 *
 * @property int $id
 * @property int $user_id
 * @property int|null $input_source_id
 * @property string $interested_in_type
 * @property int $interested_in_id
 * @property int $interest_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\InputSource|null $inputSource
 * @property-read \App\Models\Interest $interest
 * @property-read Model|\Eloquent $interestedIn
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|UserInterest allInputSources()
 * @method static \Database\Factories\UserInterestFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|UserInterest forBuilding($building)
 * @method static \Illuminate\Database\Eloquent\Builder|UserInterest forInputSource(\App\Models\InputSource $inputSource)
 * @method static \Illuminate\Database\Eloquent\Builder|UserInterest forMe(?\App\Models\User $user = null)
 * @method static \Illuminate\Database\Eloquent\Builder|UserInterest forUser($user)
 * @method static \Illuminate\Database\Eloquent\Builder|UserInterest newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserInterest newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserInterest query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserInterest residentInput()
 * @method static \Illuminate\Database\Eloquent\Builder|UserInterest whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserInterest whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserInterest whereInputSourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserInterest whereInterestId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserInterest whereInterestedInId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserInterest whereInterestedInType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserInterest whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserInterest whereUserId($value)
 * @mixin \Eloquent
 */
class UserInterest extends Model
{
    use HasFactory;

    use GetValueTrait;
    use GetMyValuesTrait;
    use ToolSettingTrait;

    protected $fillable = [
        'user_id', 'interested_in_type', 'interested_in_id', 'interest_id', 'input_source_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Return the user interest.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function interest()
    {
        return $this->belongsTo(Interest::class);
    }

    public function interestedIn()
    {
        return $this->morphTo();
    }

    public function getInterestsInServices()
    {
        $interests = [];
        /*$serviceInterests = $this->where('interested_in_type', 'element')->get();
        foreach($serviceInterests as $serviceInterest){
            $serviceInterest->interested_in_id;
            $element = Service::find($serviceInterest->interested_in_id);
            if ($element instanceof Service){
                $interests[]= $element;
            }
        }*/
        return $interests;
    }

    public function getInterestsInElements()
    {
        $interests = [];
        $serviceInterests = $this->where('interested_in_type', 'element')->get();
        /** @var self $serviceInterest */
        foreach ($serviceInterests as $serviceInterest) {
            $serviceInterest->interested_in_id;
            $element = Element::find($serviceInterest->interested_in_id);
            if ($element instanceof Element) {
                $interests[] = $element;
            }
        }

        return $interests;
    }

    public function getInterestInMeasureApplications()
    {
        $interests = [];
        $serviceInterests = $this->where('interested_in_type', 'measure_application')->get();
        /** @var self $serviceInterest */
        foreach ($serviceInterests as $serviceInterest) {
            $serviceInterest->interested_in_id;
            $element = MeasureApplication::find($serviceInterest->interested_in_id);
            if ($element instanceof MeasureApplication) {
                $interests[] = $element;
            }
        }

        return $interests;
    }

    public function getInterestsInRoofTypes()
    {
        $interests = [];
        $serviceInterests = $this->where('interested_in_type', 'roof_type')->get();
        /** @var self $serviceInterest */
        foreach ($serviceInterests as $serviceInterest) {
            $serviceInterest->interested_in_id;
            $element = RoofType::find($serviceInterest->interested_in_id);
            if ($element instanceof RoofType) {
                $interests[] = $element;
            }
        }

        return $interests;
    }

    public function getInterests()
    {
        return [
            'service' => $this->getInterestsInServices(),
            'element' => $this->getInterestsInElements(),
            'measure_application' => $this->getInterestInMeasureApplications(),
            'roof_type' => $this->getInterestsInRoofTypes(),
        ];
    }

    /**
     * Function to update or create the user interests.
     */
    public static function saveUserInterests(User $user, array $interests)
    {
        foreach ($interests as $type => $interestTypes) {
            foreach ($interestTypes as $typeId => $interestId) {
                self::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'interested_in_type' => $type,
                        'interested_in_id' => $typeId,
                    ],
                    [
                        'interest_id' => $interestId,
                    ]
                );
            }
        }
    }
}
