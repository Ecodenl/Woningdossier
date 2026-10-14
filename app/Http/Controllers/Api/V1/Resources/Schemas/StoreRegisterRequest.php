<?php

namespace App\Http\Controllers\Api\V1\Resources\Schemas;

/**
 * @OA\Schema(
 *      title="Store Register request",
 *      description="Register request body data",
 *      type="object",
 *      required={"email", "first_name", "last_name", "postal_code", "number", "street", "city"}
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
     *     title="extra",
     *     description="Extra data that may be picked up",
     *         @OA\Property(property="contact_id", example="1", type="string"),
     * )
     *
     * @var array
     */
    public $extra;

    /**
     * @OA\Property(
     *     title="tool_questions",
     *     description="Tool questions keyed by short with given answer, these are currently the only supported tool questions.",
     *         @OA\Property(property="resident-count", example=2, type="integer"),
     *         @OA\Property(property="amount-gas", example=2000, type="string"),
     *         @OA\Property(property="amount-electricity", example=2421, type="string"),
     * )
     *
     * @var array
     */
    public $tool_questions;

    /**
     * @OA\Property(
     *     property="roles",
     *     type="array",
     *     example={"resident", "coach"},
     *     @OA\Items(
     *       type="string"
     *     )
     * )
     */

    public $roles;

    /**
     * @OA\Property(
     *     title="scans",
     *     description="Scan settings for the building. When omitted, the building follows the scan settings of the cooperation.",
     *         @OA\Property(
     *             property="type",
     *             description="The scans available for the building.",
     *             type="string",
     *             enum={"quick-scan", "lite-scan", "both-scans"},
     *             example="both-scans"
     *         ),
     *         @OA\Property(
     *             property="small_measures_enabled",
     *             description="Whether the small measures step is part of the quick scan. Only allowed when the building has the quick scan, through the given type or the cooperation's scans. When omitted, the setting of the cooperation applies. The lite scan always includes small measures.",
     *             type="boolean",
     *             example=false
     *         ),
     * )
     *
     * @var array
     */
    public $scans;
}
