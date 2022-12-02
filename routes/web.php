<?php

use App\Http\Controllers\Cooperation;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Cooperation\Frontend\Tool\QuickScanController;
use App\Http\Controllers\Cooperation\Frontend\Tool\ScanController;
use App\Http\Controllers\Cooperation\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Cooperation\Auth\PasswordResetLinkController;
use App\Http\Controllers\Cooperation\Auth\RegisteredUserController;
use Laravel\Fortify\Http\Controllers\NewPasswordController;
use Laravel\Fortify\Http\Controllers\EmailVerificationNotificationController;
use Laravel\Fortify\Http\Controllers\EmailVerificationPromptController;
use Laravel\Fortify\Http\Controllers\VerifyEmailController;

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
    Route::middleware('cooperation')->name('cooperation.')->group(function () {
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

        Route::get('switch-language/{locale}', [Cooperation\UserLanguageController::class, 'switchLanguage'])->name('switch-language');

        Route::get('check-existing-mail', [RegisteredUserController::class, 'checkExistingEmail'])->name('check-existing-email');

        // Fortify auth routes start
        Route::get('/register', [RegisteredUserController::class, 'index'])
            ->middleware(['guest:' . config('fortify.guard')])
            ->name('register');
        Route::post('/register', [RegisteredUserController::class, 'store'])
            ->middleware(['guest:' . config('fortify.guard')]);

        Route::as('auth.')->group(function () {
            $limiter = config('fortify.limiters.login');
            $guard = config('fortify.guard');
            $verificationLimiter = config('fortify.limiters.verification', '6,1');

            Route::get('/email/verify', [EmailVerificationPromptController::class, '__invoke'])
                ->middleware([config('fortify.auth_middleware', 'auth') . ':' . $guard])
                ->name('verification.notice');
            Route::get('/email/verify/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
                ->middleware([config('fortify.auth_middleware', 'auth') . ':' . $guard, 'signed', 'throttle:' . $verificationLimiter])
                ->name('verification.verify');
            Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
                ->middleware([config('fortify.auth_middleware', 'auth') . ':' . $guard, 'throttle:' . $verificationLimiter])
                ->name('verification.send');

            Route::get('login', [AuthenticatedSessionController::class, 'create'])->middleware(['guest:' . $guard])
                ->name('login');
            Route::post('login', [AuthenticatedSessionController::class, 'store'])->middleware(array_filter([
                'guest:' . $guard, $limiter ? 'throttle:' . $limiter : null,
            ]))->name('login.submit');

            Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

            Route::get('password/request', [PasswordResetLinkController::class, 'create'])->middleware(['guest:' . $guard])
                ->name('password.request.index');
            Route::post('password/request', [PasswordResetLinkController::class, 'store'])->middleware(['guest:' . $guard])
                ->name('password.request.store');
            Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->middleware(['guest:' . $guard])
                ->name('password.reset');
            Route::post('reset-password', [NewPasswordController::class, 'store'])->middleware(['guest:' . $guard])
                ->name('password.update');
        });
        // Fortify auth routes end

        Route::prefix('create-building')->name('create-building.')->group(function () {
            Route::get('', [Cooperation\CreateBuildingController::class, 'index'])->name('index');
            Route::post('', [Cooperation\CreateBuildingController::class, 'store'])->name('store');
        });

        Route::name('recover-old-email.')->prefix('recover-old-email')->group(function () {
            Route::get('{token}', [Cooperation\RecoverOldEmailController::class, 'recover'])->name('recover');
        });

        Route::resource('privacy', Cooperation\PrivacyController::class)->only('index');
        Route::resource('disclaimer', Cooperation\DisclaimController::class)->only('index');

        // group can be accessed by everyone that's authorized and has a role in its session
        Route::middleware('auth', 'current-role:resident|cooperation-admin|coordinator|coach|super-admin|superuser', 'verified')->group(function () {
            Route::get('messages/count', [Cooperation\MessagesController::class, 'getTotalUnreadMessageCount'])->name('message.get-total-unread-message-count');

            if ('local' == app()->environment()) {
                // debug purpose only
                Route::name('pdf.')->prefix('pdf')->group(function () {
                    Route::name('user-report.')->prefix('user-report')->group(function () {
                        Route::get('', [Cooperation\Pdf\UserReportController::class, 'index'])->name('index');
                    });
                });
            }
            Route::get('home', [Cooperation\HomeController::class, 'index'])->name('home')->middleware('deny-if-filling-for-other-building');

            Route::prefix('file-storage')->name('file-storage.')->group(function () {
                Route::post('{fileType}', [Cooperation\FileStorageController::class, 'store'])
                    ->name('store');
                Route::get('is-being-processed/{fileType}', [Cooperation\FileStorageController::class, 'checkIfFileIsBeingProcessed'])->name('check-if-file-is-being-processed');

                Route::get('download/{fileStorage}', [Cooperation\FileStorageController::class, 'download'])
                    ->name('download');
            });

            Route::get('input-source/{input_source_value_id}', [Cooperation\InputSourceController::class, 'changeInputSourceValue'])->name('input-source.change-input-source-value');

            Route::name('messages.')->prefix('messages')->group(function () {
                Route::name('participants.')->prefix('participants')->group(function () {
                    Route::post('revoke-access', [Cooperation\Messages\ParticipantController::class, 'revokeAccess'])->name('revoke-access');

                    Route::post('add-with-building-access', [Cooperation\Messages\ParticipantController::class, 'addWithBuildingAccess'])->name('add-with-building-access');

                    Route::post('set-read', [Cooperation\Messages\ParticipantController::class, 'setRead'])->name('set-read');
                });
            });

            // my account
            Route::name('my-account.')->prefix('my-account')->middleware('track-visited-url', 'deny-if-filling-for-other-building')->group(function () {
                Route::get('', [Cooperation\MyAccount\MyAccountController::class, 'index'])->name('index');

                Route::prefix('settings')->name('settings.')->group(function () {
                    Route::put('', [Cooperation\MyAccount\SettingsController::class, 'update'])->name('update');
                    Route::delete('destroy', [Cooperation\MyAccount\SettingsController::class, 'destroy'])->name('destroy');
                    Route::post('reset-dossier', [Cooperation\MyAccount\SettingsController::class, 'resetFile'])->name('reset-file');
                });

                Route::resource('hoom-settings', Cooperation\MyAccount\HoomSettingsController::class);

                Route::name('import-center.')->prefix('import-centrum')->group(function () {
                    Route::get('set-compare-session/{inputSourceShort}', [Cooperation\MyAccount\ImportCenterController::class, 'setCompareSession'])->name('set-compare-session');
                    Route::post('dismiss-notification', [Cooperation\MyAccount\ImportCenterController::class, 'dismissNotification'])->name('dismiss-notification');
                });

                Route::resource('notification-settings', Cooperation\MyAccount\NotificationSettingsController::class)->only([
                    'index', 'show', 'update',
                ]);

                Route::name('messages.')->prefix('messages')->group(function () {
                    Route::get('', [Cooperation\MyAccount\MessagesController::class, 'index'])->name('index');
                    Route::get('edit', [Cooperation\MyAccount\MessagesController::class, 'edit'])->name('edit');
                    Route::post('edit', [Cooperation\MyAccount\MessagesController::class, 'store'])->name('store');
                });

                // the checkbox to deny the whole access for everyone.
                Route::post('access/allow-access', [Cooperation\MyAccount\AccessController::class, 'allowAccess'])->name('access.allow-access');
            });

            // conversation requests
            Route::prefix('conversation-request')->name('conversation-requests.')->group(function () {
                Route::get('{requestType}/{measureApplicationShort?}', [Cooperation\ConversationRequest\ConversationRequestController::class, 'index'])->name('index');
                Route::post('', [Cooperation\ConversationRequest\ConversationRequestController::class, 'store'])->name('store');
            });

            // the tool
            Route::prefix('import')->name('import.')->group(function () {
                Route::post('', [Cooperation\ImportController::class, 'copy'])->name('copy');
            });

            Route::as('frontend.')->middleware(['track-visited-url'])->group(function () {
                Route::resource('help', Cooperation\Frontend\HelpController::class)->only('index');
                Route::as('tool.')->group(function () {


                    $scans = \App\Helpers\Cache\Scan::allShorts();
                    // TODO: Deprecate to whereIn in L9
                    Route::get('{scan}', [Cooperation\Frontend\Tool\ScanController::class, 'redirect'])
                        ->name('scan.redirect')
                        ->where(collect(['scan'])
                            ->mapWithKeys(fn($parameter) => [$parameter => implode('|', $scans)])
                            ->all()
                        );

                    Route::prefix('{scan}/{step:slug}')
                        ->where(collect(['scan'])
                            ->mapWithKeys(fn($parameter) => [$parameter => implode('|', $scans)])
                            ->all()
                        )
                        ->as('simple-scan.')
                        ->group(function () {

                            // Define this route as last to not match above routes as step/sub step combo
                            Route::get('{subStep:slug}', [Cooperation\Frontend\Tool\SimpleScanController::class, 'index'])
                                ->name('index')
                                ->middleware(['checks-conditions-for-sub-steps', 'duplicate-data-for-user']);

                            Route::get('vragenlijst/{questionnaire}', [Cooperation\Frontend\Tool\QuickScan\QuestionnaireController::class, 'index'])
                                ->name('questionnaires.index');
                        });

                    Route::as('my-plan.')->prefix('woonplan')->group(function () {
                        Route::get('', [Cooperation\Frontend\Tool\QuickScan\MyPlanController::class, 'index'])->name('index');
                        Route::get('bestanden/{building?}', [Cooperation\Frontend\Tool\QuickScan\MyPlanController::class, 'media'])->name('media');
                    });


//                    Route::permanentRedirect('quick-scan/woonplan', 'woonplan');
                    Route::as('quick-scan.')->prefix('quick-scan')->group(function () {

                        Route::as('my-plan.')->prefix('woonplan')->group(function () {
                            Route::get('', [Cooperation\Frontend\Tool\QuickScan\MyPlanController::class, 'index'])->name('index');
                            Route::get('bestanden/{building?}', [Cooperation\Frontend\Tool\QuickScan\MyPlanController::class, 'media'])->name('media');
                        });


//                         Define this route as last to not match above routes as step/sub step combo
//                        Route::get('{step}/{subStep}', [Cooperation\Frontend\Tool\QuickScanController::class, 'index'])
//                            ->name('index')
//                            ->middleware(['checks-conditions-for-sub-steps', 'duplicate-data-for-user']);
                    });

                    Route::as('expert-scan.')->prefix('expert-scan')->group(function () {
                        // Define this route as last to not match above routes as step/sub step combo
                        Route::get('{step}', [Cooperation\Frontend\Tool\ExpertScanController::class, 'index'])
                            ->name('index')
                            ->middleware(['duplicate-data-for-user']);
                    });


                });
            });

            Route::prefix('tool')->name('tool.')->middleware('ensure-quick-scan-completed', 'track-visited-url')->group(function () {
                Route::get('/', function () {
                    return redirect()->route('cooperation.frontend.tool.quick-scan.my-plan.index');
                })->name('index');

                Route::prefix('questionnaire')->name('questionnaire.')->group(function () {
                    Route::post('', [Cooperation\Tool\QuestionnaireController::class, 'store'])->name('store');
                });

                Route::resource('example-building', Cooperation\Tool\ExampleBuildingController::class)->only('store');
                Route::resource('building-type', Cooperation\Tool\BuildingTypeController::class)->only('store');

                Route::get('heat-pump', function () {
                    Log::debug('HeatPumpController::index redirecting to heating');

                    return redirect()->route('cooperation.frontend.tool.expert-scan.index', ['step' => 'verwarming']);
                })->name('heat-pump.index');

                Route::prefix('ventilation')->name('ventilation.')->group(function () {
                    Route::resource('', Cooperation\Tool\VentilationController::class)->only('index', 'store');
                    Route::post('calculate', [Cooperation\Tool\VentilationController::class, 'calculate'])->name('calculate');
                });

                // Wall Insulation
                Route::prefix('/wall-insulation')->name('wall-insulation.')->group(function () {
                    Route::resource('', Cooperation\Tool\WallInsulationController::class)->only('index', 'store');
                    Route::post('calculate', [Cooperation\Tool\WallInsulationController::class, 'calculate'])->name('calculate');
                });

                // Wall Insulation
                Route::group(['prefix' => 'verwarming', 'as' => 'heating.'], function () {
                    Route::resource('', Cooperation\Tool\WallInsulationController::class)->only('index', 'store');
                    Route::post('calculate', [Cooperation\Tool\WallInsulationController::class, 'calculate'])->name('calculate');
                });

                // Insulated glazing
                Route::prefix('insulated-glazing')->name('insulated-glazing.')->group(function () {
                    Route::resource('', Cooperation\Tool\InsulatedGlazingController::class)->only('index', 'store');
                    Route::post('calculate', [Cooperation\Tool\InsulatedGlazingController::class, 'calculate'])->name('calculate');
                });

                // Floor Insulation
                Route::prefix('floor-insulation')->name('floor-insulation.')->group(function () {
                    Route::resource('', Cooperation\Tool\FloorInsulationController::class)->only('index', 'store');
                    Route::post('calculate', [Cooperation\Tool\FloorInsulationController::class, 'calculate'])->name('calculate');
                });

                // Roof Insulation
                Route::prefix('roof-insulation')->name('roof-insulation.')->group(function () {
                    Route::resource('', Cooperation\Tool\RoofInsulationController::class);
                    Route::post('calculate', [Cooperation\Tool\RoofInsulationController::class, 'calculate'])->name('calculate');
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
                    Route::resource('', Cooperation\Tool\SolarPanelsController::class)->only('index', 'store');
                    Route::post('calculate', [Cooperation\Tool\SolarPanelsController::class, 'calculate'])->name('calculate');
                });

                // Heater (solar boiler)
                Route::prefix('heater')->name('heater.')->group(function () {
                    Route::get('', function () {
                        Log::debug('HeaterController::index redirecting to heating');

                        return redirect()->route('cooperation.frontend.tool.expert-scan.index', ['step' => 'verwarming']);
                    })->name('index');
                });
            });

            Route::prefix('admin')->name('admin.')->middleware('role:cooperation-admin|coordinator|coach|super-admin|superuser', 'restore-building-session-if-filling-for-other-building')->group(function () {
                Route::get('/', [Cooperation\Admin\AdminController::class, 'index'])->name('index');
                Route::get('stop-session', [Cooperation\Admin\AdminController::class, 'stopSession'])->name('stop-session');
                Route::get('/switch-role/{role}', [Cooperation\Admin\SwitchRoleController::class, 'switchRole'])->name('switch-role');

                Route::prefix('roles')->name('roles.')->group(function () {
                    Route::post('assign-role', [Cooperation\Admin\RoleController::class, 'assignRole'])->name('assign-role');
                    Route::post('remove-role', [Cooperation\Admin\RoleController::class, 'removeRole'])->name('remove-role');
                });

                Route::middleware('current-role:cooperation-admin|super-admin')->group(function () {
                    Route::resource('example-buildings', Cooperation\Admin\ExampleBuildingController::class)->parameter('example-buildings', 'exampleBuilding');
                    Route::get('example-buildings/{exampleBuilding}/copy', [Cooperation\Admin\ExampleBuildingController::class, 'copy'])->name('example-buildings.copy');
                });

                /* Section that a coach, coordinator and cooperation-admin can access */
                Route::middleware('current-role:cooperation-admin|coach|coordinator')->group(function () {
                    Route::resource('messages', Cooperation\Admin\MessagesController::class)->only('index');

                    Route::prefix('tool')->name('tool.')->group(function () {
                        Route::get('fill-for-user/{building}', [Cooperation\Admin\ToolController::class, 'fillForUser'])->name('fill-for-user');
                        Route::get('observe-tool-for-user/{building}', [Cooperation\Admin\ToolController::class, 'observeToolForUser'])
                            ->name('observe-tool-for-user');
                    });

                    Route::post('message', [Cooperation\Admin\MessagesController::class, 'sendMessage'])->name('send-message');

                    Route::resource('building-notes', Cooperation\Admin\BuildingNoteController::class)->only('store');

                    Route::prefix('building-status')->name('building-status.')->group(function () {
                        Route::post('set-status', [Cooperation\Admin\BuildingStatusController::class, 'setStatus'])->name('set-status');
                        Route::post('set-appointment-date',
                            [Cooperation\Admin\BuildingStatusController::class, 'setAppointmentDate'])->name('set-appointment-date');
                    });
                });

                Route::middleware('current-role:cooperation-admin|coach|coordinator|super-admin')->group(function () {
                    Route::name('buildings.')->prefix('buildings')->group(function () {
                        Route::get('show/{buildingId}', [Cooperation\Admin\BuildingController::class, 'show'])->name('show');

                        Route::middleware('current-role:cooperation-admin|coordinator|super-admin')->group(function () {
                            Route::get('{building}/edit', [Cooperation\Admin\BuildingController::class, 'edit'])->name('edit');
                            Route::put('{building}', [Cooperation\Admin\BuildingController::class, 'update'])->name('update');
                        });
                    });
                });

                /* Section for the cooperation-admin and coordinator */
                Route::prefix('cooperatie')->name('cooperation.')->middleware('current-role:cooperation-admin|coordinator')->group(function () {
                    Route::prefix('users')->name('users.')->group(function () {
                        Route::get('', [Cooperation\Admin\Cooperation\UserController::class, 'index'])->name('index');
                        Route::get('create', [Cooperation\Admin\Cooperation\UserController::class, 'create'])->name('create');
                        Route::post('create', [Cooperation\Admin\Cooperation\UserController::class, 'store'])->name('store');

                        Route::middleware('current-role:cooperation-admin')->group(function () {
                            Route::delete('delete', [Cooperation\Admin\Cooperation\UserController::class, 'destroy'])->name('destroy');
                        });
                    });

                    Route::resource('coaches', Cooperation\Admin\Cooperation\CoachController::class)->only(['index', 'show'])
                        ->parameter('coaches', 'user');
                    Route::resource('residents', Cooperation\Admin\Cooperation\ResidentController::class)->only(['index'])
                        ->parameter('residents', 'user');

                    Route::prefix('reports')->name('reports.')->group(function () {
                        Route::get('', [Cooperation\Admin\Cooperation\ReportController::class, 'index'])->name('index');
                        Route::get('generate/{fileType}', [Cooperation\Admin\Cooperation\ReportController::class, 'generate'])->name('generate');
                    });

                    Route::resource('questionnaires', Cooperation\Admin\Cooperation\QuestionnaireController::class)
                        ->middleware('current-role:cooperation-admin');
                    // not in the cooperation-admin group, probably need to be used for the coordinator as well.
                    Route::name('questionnaires.')->prefix('questionnaire')->middleware('current-role:cooperation-admin')->group(function () {
                        Route::delete('delete-question/{questionId}', [Cooperation\Admin\Cooperation\QuestionnaireController::class, 'deleteQuestion'])->name('delete');
                        Route::delete('delete-option/{questionId}/{optionId}', [Cooperation\Admin\Cooperation\QuestionnaireController::class, 'deleteQuestionOption'])->name('delete-question-option');
                        Route::post('set-active', [Cooperation\Admin\Cooperation\QuestionnaireController::class, 'setActive'])->name('set-active');
                    });

                    /* Section for the coordinator */
                    Route::prefix('coordinator')->name('coordinator.')->middleware('current-role:coordinator')->group(function () {
                        // needs to be the last route due to the param
                        Route::get('home', [Cooperation\Admin\Cooperation\Coordinator\CoordinatorController::class, 'index'])->name('index');
                    });


                    /* section for the cooperation-admin */
                    Route::prefix('cooperation-admin')->name('cooperation-admin.')->middleware('current-role:cooperation-admin|super-admin')->group(function () {
                        // needs to be the last route due to the param
                        Route::get('home', [Cooperation\Admin\Cooperation\CooperationAdmin\CooperationAdminController::class, 'index'])->name('index');

                        Route::prefix('settings')->as('settings.')->group(function () {
                            Route::get('', [Cooperation\Admin\Cooperation\CooperationAdmin\SettingsController::class, 'index'])->name('index');
                            Route::post('', [Cooperation\Admin\Cooperation\CooperationAdmin\SettingsController::class, 'store'])->name('store');
                        });

                        Route::resource('cooperation-measure-applications', Cooperation\Admin\Cooperation\CooperationAdmin\CooperationMeasureApplicationController::class)
                            ->except(['show'])
                            ->parameter('cooperation-measure-applications', 'cooperationMeasureApplication');
                    });
                });

                /* Section for the super admin */
                Route::prefix('super-admin')->name('super-admin.')->middleware('current-role:super-admin')->group(function () {

                    Route::resource('clients', Cooperation\Admin\SuperAdmin\ClientController::class);

                    Route::resource('tool-questions', Cooperation\Admin\SuperAdmin\ToolQuestionController::class)
                        ->parameter('tool-questions', 'toolQuestion')
                        ->only(['index', 'edit', 'update']);

                    Route::resource('measure-applications', Cooperation\Admin\SuperAdmin\MeasureApplicationController::class)
                        ->parameter('measure-applications', 'measureApplication')
                        ->only(['index', 'edit', 'update']);

                    Route::prefix('{client}/api')->as('clients.personal-access-tokens.')->group(function () {
                        Route::get('', [Cooperation\Admin\SuperAdmin\Client\PersonalAccessTokenController::class, 'index'])->name('index');
                        Route::post('', [Cooperation\Admin\SuperAdmin\Client\PersonalAccessTokenController::class, 'store'])->name('store');
                        Route::get('create', [Cooperation\Admin\SuperAdmin\Client\PersonalAccessTokenController::class, 'create'])->name('create');
                        Route::delete('destroy/{personalAccessToken}', [Cooperation\Admin\SuperAdmin\Client\PersonalAccessTokenController::class, 'destroy'])->name('destroy');
                        Route::get('{personalAccessToken}/edit', [Cooperation\Admin\SuperAdmin\Client\PersonalAccessTokenController::class, 'edit'])->name('edit');
                        Route::put('{personalAccessToken}', [Cooperation\Admin\SuperAdmin\Client\PersonalAccessTokenController::class, 'update'])->name('update');
                    });

                    Route::get('home', [Cooperation\Admin\SuperAdmin\SuperAdminController::class, 'index'])->name('index');

                    Route::name('users.')->prefix('users')->group(function () {
                        Route::get('', [Cooperation\Admin\SuperAdmin\UserController::class, 'index'])->name('index');
                        Route::get('search', [Cooperation\Admin\SuperAdmin\UserController::class, 'filter'])->name('filter');
                    });

                    Route::resource('questionnaires', Cooperation\Admin\SuperAdmin\QuestionnaireController::class)->parameter('questionnaires', 'questionnaire');
                    Route::post('questionnaires/copy', [Cooperation\Admin\SuperAdmin\QuestionnaireController::class, 'copy'])->name('questionnaire.copy');
//                    Route::group(['as' => 'questionnaires.', 'prefix' => 'questionnaire'], function () {
//                        Route::get('', 'QuestionnaireController@index')->name('index');
//                        Route::get('show', 'QuestionnaireController@show')->name('show');
//                    });

                    Route::resource('key-figures', Cooperation\Admin\SuperAdmin\KeyFiguresController::class)->only('index');
                    Route::resource('translations', Cooperation\Admin\SuperAdmin\TranslationController::class)
                        ->only(['index', 'edit', 'update'])
                        ->parameter('translations', 'group');

                    /* Section for the cooperations */
                    Route::prefix('cooperations')->name('cooperations.')->group(function () {
                        Route::get('', [Cooperation\Admin\SuperAdmin\Cooperation\CooperationController::class, 'index'])->name('index');
                        Route::delete('destroy/{cooperationToDestroy}', [Cooperation\Admin\SuperAdmin\Cooperation\CooperationController::class, 'destroy'])->name('destroy');
                        Route::get('edit/{cooperationToEdit}', [Cooperation\Admin\SuperAdmin\Cooperation\CooperationController::class, 'edit'])->name('edit');
                        Route::get('create', [Cooperation\Admin\SuperAdmin\Cooperation\CooperationController::class, 'create'])->name('create');
                        Route::post('', [Cooperation\Admin\SuperAdmin\Cooperation\CooperationController::class, 'store'])->name('store');
                        Route::post('edit', [Cooperation\Admin\SuperAdmin\Cooperation\CooperationController::class, 'update'])->name('update');

                        /* Actions that will be done per cooperation */
                        Route::prefix('{cooperationToManage}/')->name('cooperation-to-manage.')->group(function () {
                            Route::resource('home', Cooperation\Admin\SuperAdmin\Cooperation\HomeController::class)->only('index');

                            Route::resource('cooperation-admin', Cooperation\Admin\SuperAdmin\Cooperation\CooperationAdminController::class)->only(['index']);
                            Route::resource('coordinator', Cooperation\Admin\SuperAdmin\Cooperation\CoordinatorController::class)->only(['index']);
                            Route::resource('users', Cooperation\Admin\SuperAdmin\Cooperation\UserController::class)->only(['index', 'show']);
                            Route::post('users/{id}/confirm', [Cooperation\Admin\SuperAdmin\Cooperation\UserController::class, 'confirm'])->name('users.confirm');
                        });
                    });
                });

                /* Section for the coach */
                Route::prefix('coach')->name('coach.')->middleware('current-role:coach')->group(function () {
                    Route::prefix('buildings')->name('buildings.')->group(function () {
                        Route::get('', [Cooperation\Admin\Coach\BuildingController::class, 'index'])->name('index');
                        Route::get('edit/{id}', [Cooperation\Admin\Coach\BuildingController::class, 'edit'])->name('edit');
                        Route::post('edit', [Cooperation\Admin\Coach\BuildingController::class, 'update'])->name('update');
                        Route::post('', [Cooperation\Admin\Coach\BuildingController::class, 'setBuildingStatus'])->name('set-building-status');

                        Route::resource('details', Cooperation\Admin\Coach\BuildingDetailsController::class)->only('store');
                    });

                    // needs to be the last route due to the param
                    Route::get('home', [Cooperation\Admin\Coach\CoachController::class, 'index'])->name('index');
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
