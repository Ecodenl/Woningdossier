<?php

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Cooperation\Frontend\Tool\QuickScanController;
use App\Http\Controllers\Cooperation\Frontend\Tool\ScanController;

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
    Route::middleware('cooperation')->name('cooperation.')->namespace('Cooperation')->group(function () {
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

        Route::namespace('Auth')->group(function () {
            Route::get('check-existing-mail', 'RegisterController@checkExistingEmail')->name('check-existing-email');
            Route::post('connect-existing-account', 'RegisterController@connectExistingAccount')->name('connect-existing-account');

            Route::get('register', 'RegisterController@showRegistrationForm')->name('register')->middleware('guest');
            Route::post('register', 'RegisterController@register');

            Route::name('auth.')->group(function () {
                Route::get('email/verify', 'VerificationController@show')->name('verification.notice');
                // for users that have some old verification url.
                Route::get('email/verify/{id}', 'VerificationController@oldVerifyUrl');
                Route::get('email/verify/{id}/{hash}', 'VerificationController@verify')->name('verification.verify');
                Route::post('email/resend', 'VerificationController@resend')->name('verification.resend');

                Route::get('login', 'LoginController@showLoginForm')->name('login')->middleware('guest');
                Route::post('login', 'LoginController@login');
//
                Route::post('logout', 'LoginController@logout')->name('logout');

                Route::prefix('password')->name('password.')->group(function () {
                    Route::get('request', 'ForgotPasswordController@index')->name('request.index');
                    Route::post('request', 'ForgotPasswordController@store')->name('request.store');

                    Route::get('reset/{token}/{email}', 'ResetPasswordController@show')->name('reset.show');
                    Route::post('reset', 'ResetPasswordController@update')->name('reset.update');
                });
            });
        });

        Route::prefix('create-building')->name('create-building.')->group(function () {
            Route::get('', 'CreateBuildingController@index')->name('index');
            Route::post('', 'CreateBuildingController@store')->name('store');
        });

        Route::name('recover-old-email.')->prefix('recover-old-email')->group(function () {
            Route::get('{token}', 'RecoverOldEmailController@recover')->name('recover');
        });

        Route::resource('privacy', 'PrivacyController')->only('index');
        Route::resource('disclaimer', 'DisclaimController')->only('index');

        // group can be accessed by everyone that's authorized and has a role in its session
        Route::middleware('auth', 'current-role:resident|cooperation-admin|coordinator|coach|super-admin|superuser', 'verified')->group(function () {
            Route::get('messages/count', 'MessagesController@getTotalUnreadMessageCount')->name('message.get-total-unread-message-count');

            if ('local' == app()->environment()) {
                // debug purpose only
                Route::name('pdf.')->namespace('Pdf')->prefix('pdf')->group(function () {
                    Route::name('user-report.')->prefix('user-report')->group(function () {
                        Route::get('', 'UserReportController@index')->name('index');
                    });
                });
            }
            Route::get('home', 'HomeController@index')->name('home')->middleware('deny-if-filling-for-other-building');

            Route::prefix('file-storage')->name('file-storage.')->group(function () {
                Route::post('{fileType}', 'FileStorageController@store')
                    ->name('store');
                Route::get('is-being-processed/{fileType}', 'FileStorageController@checkIfFileIsBeingProcessed')->name('check-if-file-is-being-processed');

                Route::get('download/{fileStorage}', 'FileStorageController@download')
                    ->name('download');
            });

            Route::get('input-source/{input_source_value_id}', 'InputSourceController@changeInputSourceValue')->name('input-source.change-input-source-value');

            Route::name('messages.')->prefix('messages')->namespace('Messages')->group(function () {
                Route::name('participants.')->prefix('participants')->group(function () {
                    Route::post('revoke-access', 'ParticipantController@revokeAccess')->name('revoke-access');

                    Route::post('add-with-building-access', 'ParticipantController@addWithBuildingAccess')->name('add-with-building-access');

                    Route::post('set-read', 'ParticipantController@setRead')->name('set-read');
                });
            });

            // my account
            Route::name('my-account.')->prefix('my-account')->namespace('MyAccount')->middleware('track-visited-url', 'deny-if-filling-for-other-building')->group(function () {
                Route::get('', 'MyAccountController@index')->name('index');

                Route::prefix('settings')->name('settings.')->group(function () {
                    Route::put('', 'SettingsController@update')->name('update');
                    Route::delete('destroy', 'SettingsController@destroy')->name('destroy');
                    Route::post('reset-dossier', 'SettingsController@resetFile')->name('reset-file');
                });

                Route::resource('hoom-settings', 'HoomSettingsController');

                Route::name('import-center.')->prefix('import-centrum')->group(function () {
                    Route::get('set-compare-session/{inputSourceShort}', 'ImportCenterController@setCompareSession')->name('set-compare-session');
                    Route::post('dismiss-notification', 'ImportCenterController@dismissNotification')->name('dismiss-notification');
                });

                Route::resource('notification-settings', 'NotificationSettingsController')->only([
                    'index', 'show', 'update',
                ]);

                Route::name('messages.')->prefix('messages')->group(function () {
                    Route::get('', 'MessagesController@index')->name('index');
                    Route::get('edit', 'MessagesController@edit')->name('edit');
                    Route::post('edit', 'MessagesController@store')->name('store');
                });

                // the checkbox to deny the whole access for everyone.
                Route::post('access/allow-access', 'AccessController@allowAccess')->name('access.allow-access');
            });

            // conversation requests
            Route::prefix('conversation-request')->name('conversation-requests.')->namespace('ConversationRequest')->group(function () {
                Route::get('{requestType}/{measureApplicationShort?}', 'ConversationRequestController@index')->name('index');
                Route::post('', 'ConversationRequestController@store')->name('store');
            });

            // the tool
            Route::prefix('import')->name('import.')->group(function () {
                Route::post('', 'ImportController@copy')->name('copy');
            });

            Route::namespace('Frontend')->as('frontend.')->middleware(['track-visited-url'])->group(function () {
                Route::resource('help', 'HelpController')->only('index');
                Route::namespace('Tool')->as('tool.')->group(function () {
                    $scans = \App\Helpers\Cache\Scan::allShorts();
                    // TODO: Deprecate to whereIn in L9
                    Route::get('{scan}', [ScanController::class, 'show'])
                        ->name('scans.show')
                        ->where(collect(['scan'])
                            ->mapWithKeys(fn ($parameter) => [$parameter => implode('|', $scans)])
                            ->all()
                        );

                    Route::as('quick-scan.')->prefix('quick-scan')->group(function () {
                        Route::get('woonplan', 'QuickScan\\MyPlanController@index')->name('my-plan.index');

                        Route::get('{step}/vragenlijst/{questionnaire}', 'QuickScan\\QuestionnaireController@index')
                            ->name('questionnaires.index');

                        // Define this route as last to not match above routes as step/sub step combo
                        Route::get('{step}/{subStep}', 'QuickScanController@index')
                            ->name('index')
                            ->middleware(['checks-conditions-for-sub-steps', 'duplicate-data-for-user']);
                    });

                    Route::as('expert-scan.')->prefix('expert-scan')->group(function () {
                        // Define this route as last to not match above routes as step/sub step combo
                        Route::get('{step}', 'ExpertScanController@index')
                        ->name('index')
                        ->middleware(['duplicate-data-for-user']);
                    });


                });
            });

            Route::prefix('tool')->name('tool.')->namespace('Tool')->middleware('ensure-quick-scan-completed', 'track-visited-url')->group(function () {
                Route::get('/', function () {
                    return redirect()->route('cooperation.frontend.tool.quick-scan.my-plan.index');
                })->name('index');

                Route::prefix('questionnaire')->name('questionnaire.')->group(function () {
                    Route::post('', 'QuestionnaireController@store')->name('store');
                });

                Route::resource('example-building', 'ExampleBuildingController')->only('store');
                Route::resource('building-type', 'BuildingTypeController')->only('store');

                Route::get('heat-pump', function () {
                    Log::debug('HeatPumpController::index redirecting to heating');

                    return redirect()->route('cooperation.frontend.tool.expert-scan.index', ['step' => 'verwarming']);
                })->name('heat-pump.index');

                Route::prefix('ventilation')->name('ventilation.')->group(function () {
                    Route::resource('', 'VentilationController')->only('index', 'store');
                    Route::post('calculate', 'VentilationController@calculate')->name('calculate');
                });

                // Wall Insulation
                Route::prefix('/wall-insulation')->name('wall-insulation.')->group(function () {
                    Route::resource('', 'WallInsulationController')->only('index', 'store');
                    Route::post('calculate', 'WallInsulationController@calculate')->name('calculate');
                });

                // Wall Insulation
                Route::group(['prefix' => 'verwarming', 'as' => 'heating.'], function () {
                    Route::resource('', 'WallInsulationController')->only('index', 'store');
                    Route::post('calculate', 'WallInsulationController@calculate')->name('calculate');
                });

                // Insulated glazing
                Route::prefix('insulated-glazing')->name('insulated-glazing.')->group(function () {
                    Route::resource('', 'InsulatedGlazingController')->only('index', 'store');
                    Route::post('calculate', 'InsulatedGlazingController@calculate')->name('calculate');
                });

                // Floor Insulation
                Route::prefix('floor-insulation')->name('floor-insulation.')->group(function () {
                    Route::resource('', 'FloorInsulationController')->only('index', 'store');
                    Route::post('calculate', 'FloorInsulationController@calculate')->name('calculate');
                });

                // Roof Insulation
                Route::prefix('roof-insulation')->name('roof-insulation.')->group(function () {
                    Route::resource('', 'RoofInsulationController');
                    Route::post('calculate', 'RoofInsulationController@calculate')->name('calculate');
                });

                // HR boiler
                Route::prefix('high-efficiency-boiler')->name('high-efficiency-boiler.')->group(function () {
                    Route::get('', function () {
                        Log::debug('HighEfficiencyBoilerController::index redirecting to heating');

                        return redirect()->route('cooperation.frontend.tool.expert-scan.index', ['step' => 'verwarming']);
                    })->name('index');
                });

                // Solar panels
                Route::prefix('solar-panels')->name('solar-panels.')->group(function () {
                    Route::resource('', 'SolarPanelsController')->only('index', 'store');
                    Route::post('calculate', 'SolarPanelsController@calculate')->name('calculate');
                });

                // Heater (solar boiler)
                Route::prefix('heater')->name('heater.')->group(function () {
                    Route::get('', function () {
                        Log::debug('HeaterController::index redirecting to heating');

                        return redirect()->route('cooperation.frontend.tool.expert-scan.index', ['step' => 'verwarming']);
                    })->name('index');
                });
            });

            Route::prefix('admin')->name('admin.')->namespace('Admin')->middleware('role:cooperation-admin|coordinator|coach|super-admin|superuser', 'restore-building-session-if-filling-for-other-building')->group(function () {
                Route::get('/', 'AdminController@index')->name('index');
                Route::get('stop-session', 'AdminController@stopSession')->name('stop-session');
                Route::get('/switch-role/{role}', 'SwitchRoleController@switchRole')->name('switch-role');

                Route::prefix('roles')->name('roles.')->group(function () {
                    Route::post('assign-role', 'RoleController@assignRole')->name('assign-role');
                    Route::post('remove-role', 'RoleController@removeRole')->name('remove-role');
                });

                Route::middleware('current-role:cooperation-admin|super-admin')->group(function () {
                    Route::resource('example-buildings', 'ExampleBuildingController')->parameter('example-buildings', 'exampleBuilding');
                    Route::get('example-buildings/{exampleBuilding}/copy', 'ExampleBuildingController@copy')->name('example-buildings.copy');
                });

                /* Section that a coach, coordinator and cooperation-admin can access */
                Route::middleware('current-role:cooperation-admin|coach|coordinator')->group(function () {
                    Route::resource('messages', 'MessagesController')->only('index');

                    Route::prefix('tool')->name('tool.')->group(function () {
                        Route::get('fill-for-user/{building}', 'ToolController@fillForUser')->name('fill-for-user');
                        Route::get('observe-tool-for-user/{building}', 'ToolController@observeToolForUser')
                            ->name('observe-tool-for-user');
                    });

                    Route::post('message', 'MessagesController@sendMessage')->name('send-message');

                    Route::resource('building-notes', 'BuildingNoteController')->only('store');

                    Route::prefix('building-status')->name('building-status.')->group(function () {
                        Route::post('set-status', 'BuildingStatusController@setStatus')->name('set-status');
                        Route::post('set-appointment-date',
                            'BuildingStatusController@setAppointmentDate')->name('set-appointment-date');
                    });
                });

                Route::middleware('current-role:cooperation-admin|coach|coordinator|super-admin')->group(function () {
                    Route::name('buildings.')->prefix('buildings')->group(function () {
                        Route::get('show/{buildingId}', 'BuildingController@show')->name('show');

                        Route::middleware('current-role:cooperation-admin|coordinator|super-admin')->group(function () {
                            Route::get('{building}/edit', 'BuildingController@edit')->name('edit');
                            Route::put('{building}', 'BuildingController@update')->name('update');
                        });
                    });
                });

                /* Section for the cooperation-admin and coordinator */
                Route::prefix('cooperatie')->name('cooperation.')->namespace('Cooperation')->middleware('current-role:cooperation-admin|coordinator')->group(function () {
                    Route::prefix('users')->name('users.')->group(function () {
                        Route::get('', 'UserController@index')->name('index');
                        Route::get('create', 'UserController@create')->name('create');
                        Route::post('create', 'UserController@store')->name('store');

                        Route::middleware('current-role:cooperation-admin')->group(function () {
                            Route::delete('delete', 'UserController@destroy')->name('destroy');
                        });
                    });

                    Route::resource('coaches', 'CoachController')->only(['index', 'show'])
                        ->parameter('coaches', 'user');
                    Route::resource('residents', 'ResidentController')->only(['index'])
                        ->parameter('residents', 'user');

                    Route::prefix('reports')->name('reports.')->group(function () {
                        Route::get('', 'ReportController@index')->name('index');
                        Route::get('generate/{fileType}', 'ReportController@generate')->name('generate');
                    });

                    Route::resource('questionnaires', 'QuestionnaireController')
                        ->middleware('current-role:cooperation-admin');
                    // not in the cooperation-admin group, probably need to be used for the coordinator as well.
                    Route::name('questionnaires.')->prefix('questionnaire')->middleware('current-role:cooperation-admin')->group(function () {
                        Route::delete('delete-question/{questionId}', 'QuestionnaireController@deleteQuestion')->name('delete');
                        Route::delete('delete-option/{questionId}/{optionId}', 'QuestionnaireController@deleteQuestionOption')->name('delete-question-option');
                        Route::post('set-active', 'QuestionnaireController@setActive')->name('set-active');
                    });

                    /* Section for the coordinator */
                    Route::prefix('coordinator')->name('coordinator.')->namespace('Coordinator')->middleware('current-role:coordinator')->group(function () {
                        // needs to be the last route due to the param
                        Route::get('home', 'CoordinatorController@index')->name('index');
                    });


                    /* section for the cooperation-admin */
                    Route::prefix('cooperation-admin')->name('cooperation-admin.')->namespace('CooperationAdmin')->middleware('current-role:cooperation-admin|super-admin')->group(function () {
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
                Route::prefix('super-admin')->name('super-admin.')->namespace('SuperAdmin')->middleware('current-role:super-admin')->group(function () {

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

                    Route::name('users.')->prefix('users')->group(function () {
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
                    Route::prefix('cooperations')->name('cooperations.')->namespace('Cooperation')->group(function () {
                        Route::get('', 'CooperationController@index')->name('index');
                        Route::delete('destroy/{cooperationToDestroy}', 'CooperationController@destroy')->name('destroy');
                        Route::get('edit/{cooperationToEdit}', 'CooperationController@edit')->name('edit');
                        Route::get('create', 'CooperationController@create')->name('create');
                        Route::post('', 'CooperationController@store')->name('store');
                        Route::post('edit', 'CooperationController@update')->name('update');

                        /* Actions that will be done per cooperation */
                        Route::prefix('{cooperationToManage}/')->name('cooperation-to-manage.')->group(function () {
                                Route::resource('home', 'HomeController')->only('index');

                                Route::resource('cooperation-admin', 'CooperationAdminController')->only(['index']);
                                Route::resource('coordinator', 'CoordinatorController')->only(['index']);
                                Route::resource('users', 'UserController')->only(['index', 'show']);
                                Route::post('users/{id}/confirm', 'UserController@confirm')->name('users.confirm');
                            });
                    });
                });

                /* Section for the coach */
                Route::prefix('coach')->name('coach.')->namespace('Coach')->middleware('current-role:coach')->group(function () {
                    Route::prefix('buildings')->name('buildings.')->group(function () {
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
