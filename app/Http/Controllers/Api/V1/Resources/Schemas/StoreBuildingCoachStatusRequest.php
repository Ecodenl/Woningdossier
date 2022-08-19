<?php

namespace App\Http\Controllers\Api\V1\Resources\Schemas;

/**
 * @OA\Schema(
 *      title="Store Building Coach Status request",
 *      description="Building Coach Status request body data",
 *      type="object",
 *      required={"building_coach_status"}
 * )
 */

class StoreBuildingCoachStatusRequest
{
    /**
     * @OA\Property(
     *     title="building_coach_status",
     *     description="Contact IDs to use for coach linking",
     *         @OA\Property(property="coach_contact_id", example=10074, type="integer"),
     *         @OA\Property(property="resident_contact_id", example=2532, type="integer"),
     * )
     *
     * @var array
     */
    public $building_coach_status;
}