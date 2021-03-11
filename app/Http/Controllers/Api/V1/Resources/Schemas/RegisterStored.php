<?php

namespace App\Http\Controllers\Api\V1\Resources\Schemas;
/**
 * @OA\Schema(
 *     title="RegisterStored",
 * )
 */

class RegisterStored
{

    /**
     * @OA\Property(
     *      title="User id",
     *      description="The id of the created user.",
     *      example="8542"
     * )
     *
     * @var int
     */
    private $user_id;

    /**
     * @OA\Property(
     *     title="Account id",
     *     description="The id of the account",
     *      example="4515"
     * )
     *
     * @var int
     */
    private $account_id;
}