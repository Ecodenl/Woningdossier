<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Cooperation\BuildingCoachStatusFormRequest;
use App\Models\Cooperation;

class ContactController extends Controller
{
    /**
     * @OA\Post(
     *      security={{"Token":{}, "X-Cooperation-Slug":{}}},
     *      path="/v1/register",
     *      operationId="storeBuildingCoachStatus",
     *      tags={"BuildingCoachStatus"},
     *      summary="Link a coach to a building.",
     *      description="Link a coach to a building.",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/StoreBuildingCoachStatusRequest")
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="OK",
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Unauthorized for current cooperation"
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Error: Unprocessable Entity"
     *      ),
     * )
     */
    public function buildingCoachStatus(BuildingCoachStatusFormRequest $request, Cooperation $cooperation)
    {
        $contactIds = $request->validated()['building_coach_status'];
        dd($contactIds);
        return response([], 200);
    }
}
