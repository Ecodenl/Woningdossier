<?php

namespace App\Http\Controllers\Api\V1;

use App\Events\UserAllowedAccessToHisBuilding;
use App\Events\UserAssociatedWithOtherCooperation;
use App\Helpers\RoleHelper;
use App\Helpers\Str;
use App\Helpers\ToolQuestionHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Cooperation\RegisterFormRequest;
use App\Mail\UserCreatedEmail;
use App\Models\Account;
use App\Models\Cooperation;
use App\Models\ToolQuestion;
use App\Models\User;
use App\Services\ToolQuestionService;
use App\Services\UserService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

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
    public function store(RegisterFormRequest $request, Cooperation $cooperation, ToolQuestionService $toolQuestionService)
    {
        $requestData = $request->all();
        if (! is_null($requestData['extra']['contact_id'] ?? null)) {
            // Force as INT
            $requestData['extra']['contact_id'] = (int) $requestData['extra']['contact_id'];
        }

        // Normally we would have a user given password, however we will reset the password right after it's created.
        // This way the user can set his own password.
        $requestData['password'] = Hash::make(Str::randomPassword());
        $roles = array_unique(($requestData['roles'] ?? [RoleHelper::ROLE_RESIDENT]));

        // With the new update in the frontend, the service has changed. This API has not. We also don't want to force
        // it out of the blue. So we convert the data to the new format.
        $requestData['address'] = [
            'postal_code' => $requestData['postal_code'],
            'number' => $requestData['number'],
            'extension' => $requestData['house_number_extension'] ?? '',
            'street' => $requestData['street'],
            'city' => $requestData['city'],
        ];

        unset(
            $requestData['postal_code'],
            $requestData['number'],
            $requestData['house_number_extension'],
            $requestData['street'],
            $requestData['city'],
        );

        $user = UserService::register($cooperation, $roles, $requestData);
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

        // At this point, a user can't register without accepting the privacy terms.
        UserAllowedAccessToHisBuilding::dispatch($user, $user->building);

        // Get input sources by name (unique)
        $inputSources = [];
        foreach ($user->roles as $role) {
            if (! is_null($role->input_source_id) && ! array_key_exists($role->input_source_id, $inputSources)) {
                $inputSources[$role->input_source_id] = $role->inputSource;
            }
        }

        // Remove indexing
        $inputSources = array_values($inputSources);

        // Ensure we don't allow nullable values
        $toolQuestionAnswers = array_filter(($requestData['tool_questions'] ?? []), function ($value) {
            return ! is_null($value);
        });

        $toolQuestionService->building($user->building);
        foreach ($toolQuestionAnswers as $toolQuestionShort => $toolQuestionAnswer) {
            if (in_array($toolQuestionShort, ToolQuestionHelper::SUPPORTED_API_SHORTS)) {
                $toolQuestion = ToolQuestion::findByShort($toolQuestionShort);

                if ($toolQuestion instanceof ToolQuestion) {
                    foreach ($inputSources as $inputSource) {
                        $toolQuestionService->toolQuestion($toolQuestion)
                            ->currentInputSource($inputSource)
                            ->save($toolQuestionAnswer);
                    }
                }
            }
        }

        $client = $request->user();

        $logContext = [
            'users' => [
                ['account_id' => $account->id, 'user_id' => $user->id]
            ],
            'client' => Arr::only($client->attributesToArray(), ['id', 'name']),
        ];

        if (!app()->runningUnitTests()) {
            // the access token in mocked in a broken way.
            $logContext['personal_access_token'] = Arr::only($client->currentAccessToken()->attributesToArray(), ['id', 'name']);
        }
        Log::channel('api')->info('User registered', $logContext);

        return response(['account_id' => $account->id, 'user_id' => $user->id], 201);
    }

    private function sendAccountConfirmationMail(Cooperation $cooperation, Account $account)
    {
        $token = app('auth.password.broker')->createToken($account);

        // send a mail to the user
        Mail::to($account->email)->send(new UserCreatedEmail($cooperation, $account->user(), $token));
    }
}
