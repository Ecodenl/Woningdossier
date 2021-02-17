<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller as BaseController;
use Illuminate\Http\Request;

class Controller extends BaseController
{
    /**
     * @OA\Info(
     *      version="1.0.0",
     *      title="Hoomdossier API documentation",
     *      description="Bliep",
     *      @OA\Contact(
     *          email="info@wedesignit.nl"
     *      ),
     *      @OA\License(
     *          name="Apache 2.0",
     *          url="http://www.apache.org/licenses/LICENSE-2.0.html"
     *      )
     * )
     *
     * @OA\Server(
     *      url=L5_SWAGGER_CONST_HOST,
     *      description="API v1"
     * )
     *
     * @OAS\SecurityScheme(
     *      securityScheme="bearer_token",
     *      type="http",
     *      scheme="bearer"
     * )
     *
     * @OA\Tag(
     *     name="Ping",
     *     description="Endpoints for connection checks"
     * )
     *
     * @OA\Tag(
     *     name="Register",
     *     description="Endpoint to register a new user "
     * )
     */


    /**
     * @OA\Get(
     *      security={{"bearer_token":{}}},
     *      path="/v1/",
     *      tags={"Ping"},
     *      summary="API Endpoint voor connectie check",
     *      description="Simple ping to check if its a proper request.",
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Unauthorized for current cooperation"
     *      )
     * )
     */
    public function index()
    {
        return response([], 200);
    }
}
