<?php

namespace App\Http\Controllers\Api\V1;

use App\Events\UserAllowedAccessToHisBuilding;
use App\Events\UserAssociatedWithOtherCooperation;
use App\Helpers\Str;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Cooperation\RegisterFormRequest;
use App\Mail\UserCreatedEmail;
use App\Models\Account;
use App\Models\Cooperation;
use App\Services\UserService;

class RegisterController extends Controller
{
    /**
     * @OA\Post(
     *      security={{"Token":{}, "X-Cooperation-Slug":{}}},
     *      path="/v1/register",
     *      operationId="storeProject",
     *      tags={"Register"},
     *      summary="Register a new user.",
     *      description="Returns a user and account id.",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/StoreRegisterRequest")
     *      ),
     *
     *      @OA\Response(
     *          response=201,
     *          description="Created",
     *          @OA\JsonContent(
     *              ref="#/components/schemas/RegisterStored",
     *              @OA\Schema(
     *                  title="RegisterStored",
     *                  description="",
     *                  @OA\Xml(
     *                      name="RegisterStored"
     *                  )
     *              )
     *          ),
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
    public function store(RegisterFormRequest $request, Cooperation $cooperation)
    {
        $requestData = $request->all();

        // normally we would have a user given password, however we will reset the password right after its created.
        // this way the user can set his own password.
        $requestData['password'] = \Hash::make(Str::randomPassword());
        $user = UserService::register($cooperation, ['resident'], $requestData);
        $account = $user->account;

        // if the account is recently created we have to send a confirmation mail
        // else we send a notification to the user he is associated with a new cooperation
        if ($account->wasRecentlyCreated) {
            // and send the account confirmation mail.
            $this->sendAccountConfirmationMail($cooperation, $account);
            $account->markEmailAsVerified();
        } else {
            UserAssociatedWithOtherCooperation::dispatch($cooperation, $user);
        }

        // at this point, a user cant register without accepting the privacy terms.
        UserAllowedAccessToHisBuilding::dispatch($user->building);

        return response(['account_id' => $account->id, 'user_id' => $user->id], 201);
    }

    private function sendAccountConfirmationMail(Cooperation $cooperation, Account $account)
    {
        $token = app('auth.password.broker')->createToken($account);

        // send a mail to the user
        \Mail::to($account->email)->sendNow(new UserCreatedEmail($cooperation, $account->user(), $token));
    }
}
