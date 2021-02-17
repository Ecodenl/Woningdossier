<?php

/**
 * @OA\Schema(
 *      title="Store Register request",
 *      description="Register request body data",
 *      type="object",
 *      required={"email", "first_name", "last_name", "postal_code", "number", "street", "city", "contact_id"}
 * )
 */

class StoreRegisterRequest
{

    /**
     * @OA\Property(
     *      title="email",
     *      description="The email of the account",
     *      example="demo@example.org"
     * )
     *
     * @var string
     */
    public $email;

    /**
     * @OA\Property(
     *      title="first_name",
     *      description="The first_name of the user",
     *      example="Erica"
     * )
     *
     * @var string
     */
    public $first_name;

    /**
     * @OA\Property(
     *      title="last_name",
     *      description="The last_name of the user",
     *      example="Bubba"
     * )
     *
     * @var string
     */
    public $last_name;

    /**
     * @OA\Property(
     *      title="Postal code",
     *      description="The postal code of the user",
     *      example="1234AB"
     * )
     *
     * @var string
     */
    public $postal_code;

    /**
     * @OA\Property(
     *      title="number",
     *      description="The house number of the user",
     *      example="10"
     * )
     *
     * @var string
     */
    public $number;

    /**
     * @OA\Property(
     *      title="House number extension",
     *      description="The extension of the house number",
     *      example="10"
     * )
     *
     * @var string
     */
    public $house_number_extension;

    /**
     * @OA\Property(
     *      title="Street",
     *      description="The street of the user",
     *      example="Streetsway"
     * )
     *
     * @var string
     */
    public $street;

    /**
     * @OA\Property(
     *      title="City",
     *      description="The city of the user",
     *      example="Citystadt"
     * )
     *
     * @var string
     */
    public $city;

    /**
     * @OA\Property(
     *      title="phone_number",
     *      description="The phone_number of the user",
     *      example="0612345678"
     * )
     *
     * @var string
     */
    public $phone_number;

    /**
     * @OA\Property(
     *      title="extra",
     *      description="Extra data that may be picked up",
     *     @OA\Items(type="string",example={"contact_id":"1"}),
     * )
     *
     * @var array
     */
    public $extra;

}
