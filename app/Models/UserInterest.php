<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\GetMyValuesTrait;
use App\Traits\GetValueTrait;

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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserInterest allInputSources()
 * @method static \Database\Factories\UserInterestFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserInterest forBuilding(\App\Models\Building|int $building)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserInterest forInputSource(\App\Models\InputSource $inputSource)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserInterest forMe(?\App\Models\User $user = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserInterest forUser(\App\Models\User|int $user)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserInterest newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserInterest newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserInterest query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserInterest residentInput()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserInterest whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserInterest whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserInterest whereInputSourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserInterest whereInterestId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserInterest whereInterestedInId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserInterest whereInterestedInType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserInterest whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserInterest whereUserId($value)
 * @mixin \Eloquent
 */
class UserInterest extends Model
{
    use HasFactory;

    use GetValueTrait;
    use GetMyValuesTrait;
    

    protected $fillable = [
        'user_id', 'interested_in_type', 'interested_in_id', 'interest_id', 'input_source_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Return the user interest.
     */
    public function interest(): BelongsTo
    {
        return $this->belongsTo(Interest::class);
    }

    public function interestedIn(): MorphTo
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
