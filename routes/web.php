<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Cooperation\Frontend\Tool\QuickScanController;

/** @noinspection PhpParamsInspection */

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::domain('{cooperation}.' . config('hoomdossier.domain'))->group(function () {
    Route::group(['middleware' => 'cooperation', 'as' => 'cooperation.', 'namespace' => 'Cooperation'], function () {
        if ('local' == app()->environment()) {
            Route::get('mail', function () {
//            return new \App\Mail\UserCreatedEmail(\App\Models\Cooperation::find(1), \App\Models\User::find(1), 'sdfkhasgdfuiasdgfyu');
//            return new \App\Mail\UserAssociatedWithCooperation(\App\Models\Cooperation::find(1), \App\Models\User::find(1));
//            return new \App\Mail\UserChangedHisEmail(\App\Models\User::find(1), \App\Models\Account::find(1), 'demo@eg.com', 'bier@pils.com');
                return new  \App\Mail\UnreadMessagesEmail(\App\Models\User::find(1), \App\Models\Cooperation::find(1), 10);
//            return new \App\Mail\ResetPasswordRequest(\App\Models\Cooperation::find(1), \App\Models\Account::find(1), 'sfklhasdjkfhsjkf');
//            return new \App\Mail\RequestAccountConfirmationEmail(\App\Models\User::find(1), \App\Models\Cooperation::find(1));
            });
        }

        Route::view('styleguide', 'cooperation.frontend.styleguide');
        Route::view('input-guide', 'cooperation.frontend.input-guide');

        Route::get('/', function () {
            return view('cooperation.welcome');
        })->name('welcome');

        Route::get('switch-language/{locale}', 'UserLanguageController@switchLanguage')->name('switch-language');

        Route::group(['namespace' => 'Auth',], function () {
            Route::get('check-existing-mail', 'RegisterController@checkExistingEmail')->name('check-existing-email');
            Route::post('connect-existing-account', 'RegisterController@connectExistingAccount')->name('connect-existing-account');

            Route::get('register', 'RegisterController@showRegistrationForm')->name('register')->middleware('guest');
            Route::post('register', 'RegisterController@register');

            Route::group(['as' => 'auth.'], function () {
                Route::get('email/verify', 'VerificationController@show')->name('verification.notice');
                // for users that have some old verification url.
                Route::get('email/verify/{id}', 'VerificationController@oldVerifyUrl');
                Route::get('email/verify/{id}/{hash}', 'VerificationController@verify')->name('verification.verify');
                Route::post('email/resend', 'VerificationController@resend')->name('verification.resend');

                Route::get('login', 'LoginController@showLoginForm')->name('login')->middleware('guest');
                Route::post('login', 'LoginController@login');
//
                Route::post('logout', 'LoginController@logout')->name('logout');

                Route::group(['prefix' => 'password', 'as' => 'password.'], function () {
                    Route::get('request', 'ForgotPasswordController@index')->name('request.index');
                    Route::post('request', 'ForgotPasswordController@store')->name('request.store');

                    Route::get('reset/{token}/{email}', 'ResetPasswordController@show')->name('reset.show');
                    Route::post('reset', 'ResetPasswordController@update')->name('reset.update');
                });
            });
        });

        Route::group(['prefix' => 'create-building', 'as' => 'create-building.'], function () {
            Route::get('', 'CreateBuildingController@index')->name('index');
            Route::post('', 'CreateBuildingController@store')->name('store');
        });

        Route::group(['as' => 'recover-old-email.', 'prefix' => 'recover-old-email'], function () {
            Route::get('{token}', 'RecoverOldEmailController@recover')->name('recover');
        });

        Route::resource('privacy', 'PrivacyController')->only('index');
        Route::resource('disclaimer', 'DisclaimController')->only('index');

        // group can be accessed by everyone that's authorized and has a role in its session
        Route::group(['middleware' => ['auth', 'current-role:resident|cooperation-admin|coordinator|coach|super-admin|superuser', 'verified']], function () {
            Route::get('messages/count', 'MessagesController@getTotalUnreadMessageCount')->name('message.get-total-unread-message-count');
            Route::get('notifications', 'NotificationController@index')->name('notifications.index');

            if ('local' == app()->environment()) {
                // debug purpose only
                Route::group(['as' => 'pdf.', 'namespace' => 'Pdf', 'prefix' => 'pdf'], function () {
                    Route::group(['as' => 'user-report.', 'prefix' => 'user-report'], function () {
                        Route::get('', 'UserReportController@index')->name('index');
                    });
                });
            }
            Route::get('home', 'HomeController@index')->name('home')->middleware('deny-if-filling-for-other-building');

            Route::group(['prefix' => 'file-storage', 'as' => 'file-storage.'], function () {
                Route::post('{fileType}', 'FileStorageController@store')
                    ->name('store');
                Route::get('is-being-processed/{fileType}', 'FileStorageController@checkIfFileIsBeingProcessed')->name('check-if-file-is-being-processed');

                Route::get('download/{fileStorage}', 'FileStorageController@download')
                    ->name('download');
            });

            Route::get('input-source/{input_source_value_id}', 'InputSourceController@changeInputSourceValue')->name('input-source.change-input-source-value');

            Route::group(['as' => 'messages.', 'prefix' => 'messages', 'namespace' => 'Messages'], function () {
                Route::group(['as' => 'participants.', 'prefix' => 'participants'], function () {
                    Route::post('revoke-access', 'ParticipantController@revokeAccess')->name('revoke-access');

                    Route::post('add-with-building-access', 'ParticipantController@addWithBuildingAccess')->name('add-with-building-access');

                    Route::post('set-read', 'ParticipantController@setRead')->name('set-read');
                });
            });

            // my account
            Route::group(['as' => 'my-account.', 'prefix' => 'my-account', 'namespace' => 'MyAccount', 'middleware' => ['track-visited-url', 'deny-if-filling-for-other-building']], function () {
                Route::get('', 'MyAccountController@index')->name('index');

                Route::group(['prefix' => 'settings', 'as' => 'settings.'], function () {
                    Route::put('', 'SettingsController@update')->name('update');
                    Route::delete('destroy', 'SettingsController@destroy')->name('destroy');
                    Route::post('reset-dossier', 'SettingsController@resetFile')->name('reset-file');
                });

                Route::resource('hoom-settings', 'HoomSettingsController');

                Route::group(['as' => 'import-center.', 'prefix' => 'import-centrum'], function () {
                    Route::get('set-compare-session/{inputSourceShort}', 'ImportCenterController@setCompareSession')->name('set-compare-session');
                    Route::post('dismiss-notification', 'ImportCenterController@dismissNotification')->name('dismiss-notification');
                });

                Route::resource('notification-settings', 'NotificationSettingsController')->only([
                    'index', 'show', 'update',
                ]);

                Route::group(['as' => 'messages.', 'prefix' => 'messages'], function () {
                    Route::get('', 'MessagesController@index')->name('index');
                    Route::get('edit', 'MessagesController@edit')->name('edit');
                    Route::post('edit', 'MessagesController@store')->name('store');
                });

                // the checkbox to deny the whole access for everyone.
                Route::post('access/allow-access', 'AccessController@allowAccess')->name('access.allow-access');
            });

            // conversation requests
            Route::group(['prefix' => 'conversation-request', 'as' => 'conversation-requests.', 'namespace' => 'ConversationRequest'], function () {
                Route::get('{requestType}/{measureApplicationShort?}', 'ConversationRequestController@index')->name('index');
                Route::post('', 'ConversationRequestController@store')->name('store');
            });

            // the tool
            Route::group(['prefix' => 'import', 'as' => 'import.'], function () {
                Route::post('', 'ImportController@copy')->name('copy');
            });

            Route::namespace('Frontend')->as('frontend.')->middleware(['track-visited-url'])->group(function () {
                Route::resource('help', 'HelpController')->only('index');

                Route::namespace('Tool')->as('tool.')->group(function () {
                    Route::as('quick-scan.')->prefix('quick-scan')->group(function () {
                        Route::get('', [QuickScanController::class, 'start'])->name('start');
                        Route::get('woonplan', 'QuickScan\\MyPlanController@index')->name('my-plan.index');

                        Route::get('{step}/vragenlijst/{questionnaire}', 'QuickScan\\QuestionnaireController@index')
                            ->name('questionnaires.index');

                        // Define this route as last to not match above routes as step/sub step combo
                        Route::get('{step}/{subStep}', 'QuickScanController@index')
                            ->name('index')
                            ->middleware(['checks-conditions-for-sub-steps', 'duplicate-data-for-user']);
                    });
                });
            });

            Route::group(['prefix' => 'tool', 'as' => 'tool.', 'namespace' => 'Tool', 'middleware' => ['ensure-quick-scan-completed', 'track-visited-url']], function () {
                Route::get('/', function () {
                    return redirect()->route('cooperation.frontend.tool.quick-scan.my-plan.index');
                })->name('index');

                Route::group(['prefix' => 'questionnaire', 'as' => 'questionnaire.'], function () {
                    Route::post('', 'QuestionnaireController@store')->name('store');
                });

                Route::resource('example-building', 'ExampleBuildingController')->only('store');
                Route::resource('building-type', 'BuildingTypeController')->only('store');

//                Route::group(['as' => 'general-data.', 'prefix' => 'general-data'], function () {
//                    Route::get('', 'GeneralDataController@index')->name('index');
//
//                    Route::group(['namespace' => 'GeneralData'], function () {
//                        Route::resource('gebouw-kenmerken', 'BuildingCharacteristicsController')->only(['index', 'store'])->names('building-characteristics');
//                        Route::get('get-qualified-example-buildings', 'BuildingCharacteristicsController@qualifiedExampleBuildings')->name('building-characteristics.qualified-example-buildings');
//
//                        Route::resource('huidige-staat', 'CurrentStateController')->names('current-state')->only(['index', 'store']);
//                        Route::resource('gebruik', 'UsageController')->only(['index', 'store'])->names('usage');
//                    });
//                });

//                Route::group(['middleware' => 'filled-step:general-data'], function () {
                    // Heat pump: info for now
                    Route::resource('heat-pump', 'HeatPumpController', ['only' => ['index', 'store']]);

                    Route::group(['prefix' => 'ventilation', 'as' => 'ventilation.'], function () {
                        Route::resource('', 'VentilationController', ['only' => ['index', 'store']]);
                        Route::post('calculate', 'VentilationController@calculate')->name('calculate');
                    });

                    // Wall Insulation
                    Route::group(['prefix' => 'wall-insulation', 'as' => 'wall-insulation.'], function () {
                        Route::resource('', 'WallInsulationController', ['only' => ['index', 'store']]);
                        Route::post('calculate', 'WallInsulationController@calculate')->name('calculate');
                    });

                    // Insulated glazing
                    Route::group(['prefix' => 'insulated-glazing', 'as' => 'insulated-glazing.'], function () {
                        Route::resource('', 'InsulatedGlazingController', ['only' => ['index', 'store']]);
                        Route::post('calculate', 'InsulatedGlazingController@calculate')->name('calculate');
                    });

                    // Floor Insulation
                    Route::group(['prefix' => 'floor-insulation', 'as' => 'floor-insulation.'], function () {
                        Route::resource('', 'FloorInsulationController', ['only' => ['index', 'store']]);
                        Route::post('calculate', 'FloorInsulationController@calculate')->name('calculate');
                    });

                    // Roof Insulation
                    Route::group(['prefix' => 'roof-insulation', 'as' => 'roof-insulation.'], function () {
                        Route::resource('', 'RoofInsulationController');
                        Route::post('calculate', 'RoofInsulationController@calculate')->name('calculate');
                    });

                    // HR boiler
                    Route::group(['prefix' => 'high-efficiency-boiler', 'as' => 'high-efficiency-boiler.'], function () {
                        Route::resource('', 'HighEfficiencyBoilerController', ['only' => ['index', 'store']]);
                        Route::post('calculate', 'HighEfficiencyBoilerController@calculate')->name('calculate');
                    });

                    // Solar panels
                    Route::group(['prefix' => 'solar-panels', 'as' => 'solar-panels.'], function () {
                        Route::resource('', 'SolarPanelsController', ['only' => ['index', 'store']]);
                        Route::post('calculate', 'SolarPanelsController@calculate')->name('calculate');
                    });

                    // Heater (solar boiler)
                    Route::group(['prefix' => 'heater', 'as' => 'heater.'], function () {
                        Route::resource('', 'HeaterController', ['only' => ['index', 'store']]);
                        Route::post('calculate', 'HeaterController@calculate')->name('calculate');
                    });
//                });

//                Route::get('my-plan', 'MyPlanController@index')->name('my-plan.index');
//                Route::post('my-plan/comment', 'MyPlanController@storeComment')
//                    ->middleware('deny-if-observing-building')
//                    ->name('my-plan.store-comment');
//                Route::post('my-plan/store', 'MyPlanController@store')->name('my-plan.store');
//                Route::get('my-plan/export', 'MyPlanController@export')->name('my-plan.export');
            });

            Route::group(['prefix' => 'admin', 'as' => 'admin.', 'namespace' => 'Admin',
                'middleware' => [
                    'role:cooperation-admin|coordinator|coach|super-admin|superuser',
                    'restore-building-session-if-filling-for-other-building',
                ],
            ], function () {
                Route::get('/', 'AdminController@index')->name('index');
                Route::get('stop-session', 'AdminController@stopSession')->name('stop-session');
                Route::get('/switch-role/{role}', 'SwitchRoleController@switchRole')->name('switch-role');

                Route::group(['prefix' => 'roles', 'as' => 'roles.'], function () {
                    Route::post('assign-role', 'RoleController@assignRole')->name('assign-role');
                    Route::post('remove-role', 'RoleController@removeRole')->name('remove-role');
                });

                Route::group(['middleware' => ['current-role:cooperation-admin|super-admin']], function () {
                    Route::resource('example-buildings', 'ExampleBuildingController')->parameter('example-buildings', 'exampleBuilding');
                    Route::get('example-buildings/{exampleBuilding}/copy', 'ExampleBuildingController@copy')->name('example-buildings.copy');
                });

                /* Section that a coach, coordinator and cooperation-admin can access */
                Route::group(['middleware' => ['current-role:cooperation-admin|coach|coordinator']], function () {
                    Route::resource('messages', 'MessagesController')->only('index');

                    Route::group(['prefix' => 'tool', 'as' => 'tool.'], function () {
                        Route::get('fill-for-user/{building}', 'ToolController@fillForUser')->name('fill-for-user');
                        Route::get('observe-tool-for-user/{building}', 'ToolController@observeToolForUser')
                            ->name('observe-tool-for-user');
                    });

                    Route::post('message', 'MessagesController@sendMessage')->name('send-message');

                    Route::resource('building-notes', 'BuildingNoteController')->only('store');

                    Route::group(['prefix' => 'building-status', 'as' => 'building-status.'], function () {
                        Route::post('set-status', 'BuildingStatusController@setStatus')->name('set-status');
                        Route::post('set-appointment-date',
                            'BuildingStatusController@setAppointmentDate')->name('set-appointment-date');
                    });
                });

                Route::group(['middleware' => ['current-role:cooperation-admin|coach|coordinator|super-admin']], function () {
                    Route::group(['as' => 'buildings.', 'prefix' => 'buildings'], function () {
                        Route::get('show/{buildingId}', 'BuildingController@show')->name('show');

                        Route::group(['middleware' => ['current-role:cooperation-admin|coordinator|super-admin']], function () {
                            Route::get('{building}/edit', 'BuildingController@edit')->name('edit');
                            Route::put('{building}', 'BuildingController@update')->name('update');
                        });
                    });
                });

                /* Section for the cooperation-admin and coordinator */
                Route::group(['prefix' => 'cooperatie', 'as' => 'cooperation.', 'namespace' => 'Cooperation', 'middleware' => ['current-role:cooperation-admin|coordinator']], function () {
                    Route::group(['prefix' => 'users', 'as' => 'users.'], function () {
                        Route::get('', 'UserController@index')->name('index');
                        Route::get('create', 'UserController@create')->name('create');
                        Route::post('create', 'UserController@store')->name('store');

                        Route::group(['middleware' => 'current-role:cooperation-admin'], function () {
                            Route::delete('delete', 'UserController@destroy')->name('destroy');
                        });
                    });

                    Route::resource('coaches', 'CoachController')->only(['index', 'show'])->parameter('coaches', 'user');

                    Route::group(['prefix' => 'reports', 'as' => 'reports.'], function () {
                        Route::get('', 'ReportController@index')->name('index');
                        Route::get('generate/{fileType}', 'ReportController@generate')->name('generate');
                    });

                    Route::resource('questionnaires', 'QuestionnaireController')
                        ->middleware('current-role:cooperation-admin');
                    // not in the cooperation-admin group, probably need to be used for the coordinator as well.
                    Route::group(['as' => 'questionnaires.', 'prefix' => 'questionnaire', 'middleware' => ['current-role:cooperation-admin']], function () {
                        Route::delete('delete-question/{questionId}', 'QuestionnaireController@deleteQuestion')->name('delete');
                        Route::delete('delete-option/{questionId}/{optionId}', 'QuestionnaireController@deleteQuestionOption')->name('delete-question-option');
                        Route::post('set-active', 'QuestionnaireController@setActive')->name('set-active');
                    });

                    /* Section for the coordinator */
                    Route::group(['prefix' => 'coordinator', 'as' => 'coordinator.', 'namespace' => 'Coordinator', 'middleware' => ['current-role:coordinator']], function () {
                        // needs to be the last route due to the param
                        Route::get('home', 'CoordinatorController@index')->name('index');
                    });


                    /* section for the cooperation-admin */
                    Route::group(['prefix' => 'cooperation-admin', 'as' => 'cooperation-admin.', 'namespace' => 'CooperationAdmin', 'middleware' => ['current-role:cooperation-admin|super-admin']], function () {
                        // needs to be the last route due to the param
                        Route::get('home', 'CooperationAdminController@index')->name('index');

                        Route::prefix('settings')->as('settings.')->group(function () {
                            Route::get('', 'SettingsController@index')->name('index');
                            Route::post('', 'SettingsController@store')->name('store');
                        });

                        Route::resource('cooperation-measure-applications', 'CooperationMeasureApplicationController')
                            ->except(['show'])
                            ->parameter('cooperation-measure-applications', 'cooperationMeasureApplication');
                    });
                });

                /* Section for the super admin */
                Route::group(['prefix' => 'super-admin', 'as' => 'super-admin.', 'namespace' => 'SuperAdmin', 'middleware' => ['current-role:super-admin']], function () {

                    Route::resource('clients', 'ClientController');

                    Route::resource('tool-questions', 'ToolQuestionController')
                        ->parameter('tool-questions', 'toolQuestion')
                        ->only(['index', 'edit', 'update']);

                    Route::resource('measure-applications', 'MeasureApplicationController')
                        ->parameter('measure-applications', 'measureApplication')
                        ->only(['index', 'edit', 'update']);

                    Route::prefix('{client}/api')->namespace('Client')->as('clients.personal-access-tokens.')->group(function () {
                        Route::get('', 'PersonalAccessTokenController@index')->name('index');
                        Route::post('', 'PersonalAccessTokenController@store')->name('store');
                        Route::get('create', 'PersonalAccessTokenController@create')->name('create');
                        Route::delete('destroy/{personalAccessToken}', 'PersonalAccessTokenController@destroy')->name('destroy');
                        Route::get('{personalAccessToken}/edit', 'PersonalAccessTokenController@edit')->name('edit');
                        Route::put('{personalAccessToken}', 'PersonalAccessTokenController@update')->name('update');
                    });

                    Route::get('home', 'SuperAdminController@index')->name('index');

                    Route::group(['as' => 'users.', 'prefix' => 'users'], function () {
                        Route::get('', 'UserController@index')->name('index');
                        Route::get('search', 'UserController@filter')->name('filter');
                    });

                    Route::resource('questionnaires', 'QuestionnaireController')->parameter('questionnaires', 'questionnaire');
                    Route::post('questionnaires/copy', 'QuestionnaireController@copy')->name('questionnaire.copy');
//                    Route::group(['as' => 'questionnaires.', 'prefix' => 'questionnaire'], function () {
//                        Route::get('', 'QuestionnaireController@index')->name('index');
//                        Route::get('show', 'QuestionnaireController@show')->name('show');
//                    });

                    Route::resource('key-figures', 'KeyFiguresController')->only('index');
                    Route::resource('translations', 'TranslationController')
                        ->only(['index', 'edit', 'update'])
                        ->parameter('translations', 'group');

                    /* Section for the cooperations */
                    Route::group(['prefix' => 'cooperations', 'as' => 'cooperations.', 'namespace' => 'Cooperation'], function () {
                        Route::get('', 'CooperationController@index')->name('index');
                        Route::delete('destroy/{cooperationToDestroy}', 'CooperationController@destroy')->name('destroy');
                        Route::get('edit/{cooperationToEdit}', 'CooperationController@edit')->name('edit');
                        Route::get('create', 'CooperationController@create')->name('create');
                        Route::post('', 'CooperationController@store')->name('store');
                        Route::post('edit', 'CooperationController@update')->name('update');

                        /* Actions that will be done per cooperation */
                        Route::group(['prefix' => '{cooperationToManage}/', 'as' => 'cooperation-to-manage.'],
                            function () {
                                Route::resource('home', 'HomeController')->only('index');

                                Route::resource('cooperation-admin', 'CooperationAdminController')->only(['index']);
                                Route::resource('coordinator', 'CoordinatorController')->only(['index']);
                                Route::resource('users', 'UserController')->only(['index', 'show']);
                                Route::post('users/{id}/confirm', 'UserController@confirm')->name('users.confirm');
                            });
                    });
                });

                /* Section for the coach */
                Route::group(['prefix' => 'coach', 'as' => 'coach.', 'namespace' => 'Coach', 'middleware' => ['current-role:coach']], function () {
                    Route::group(['prefix' => 'buildings', 'as' => 'buildings.'], function () {
                        Route::get('', 'BuildingController@index')->name('index');
                        Route::get('edit/{id}', 'BuildingController@edit')->name('edit');
                        Route::post('edit', 'BuildingController@update')->name('update');
                        Route::post('', 'BuildingController@setBuildingStatus')->name('set-building-status');

                        Route::resource('details', 'BuildingDetailsController')->only('store');
                    });

                    // needs to be the last route due to the param
                    Route::get('home', 'CoachController@index')->name('index');
                });
            });
        });
    });
});

Route::get('/', function () {
    if (stristr(\Request::url(), '://www.')) {
        // The user has prefixed the subdomain with a www subdomain.
        // Remove the www part and redirect to that.
        return redirect(str_replace('://www.', '://', Request::url()));
    }

    return view('welcome');
})->name('index');
