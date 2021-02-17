<?php

/**
 * @OA\Schema(
 *     title="RegisterStored",
 *     description="",
 *     @OA\Xml(
 *         name="ApiStatus"
 *     )
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
     *     description="The id of the account"
     * )
     *
     * @var int
     */
    private $account_id;
}