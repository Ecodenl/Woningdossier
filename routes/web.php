<?php

use App\Http\Controllers\Cooperation;
use App\Http\Controllers\Cooperation\Admin\Cooperation\CooperationAdmin\CooperationMeasureApplicationController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

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

// When a user goes to www.hoomdossier.nl, it aborts as 404 since it's failing to find cooperation "www". While
// technically correct, it can be confusing. We just redirect them to the index page.
Route::domain('www.' . config('hoomdossier.domain'))->group(function () {
    // Can't call route('index') since it will keep the www. domain.
    Route::get('', fn() => redirect(str_replace('://www.', '://', Request::url())));
    // Non-existent route, fall back to index.
    Route::fallback(fn() => redirect()->route('index'));
});

Route::domain('{cooperation}.' . config('hoomdossier.domain'))->group(function () {
    Route::middleware('cooperation')->name('cooperation.')->group(function () {
        if ('local' == app()->environment()) {
            Route::get('mail', function () {
                //return new \App\Mail\UserCreatedEmail(\App\Models\Cooperation::find(1), \App\Models\User::find(1), 'sdfkhasgdfuiasdgfyu');
                //return new \App\Mail\UserAssociatedWithCooperation(\App\Models\Cooperation::find(1), \App\Models\User::find(1));
                //return new \App\Mail\UserChangedHisEmail(\App\Models\User::find(1), \App\Models\Account::find(1), 'demo@eg.com', 'bier@pils.com');
                //return new  \App\Mail\UnreadMessagesEmail(\App\Models\User::find(1), \App\Models\Cooperation::find(1), 10);
                //return new \App\Mail\ResetPasswordRequest(\App\Models\Cooperation::find(1), \App\Models\Account::find(1), 'sfklhasdjkfhsjkf');
                //return new \App\Mail\RequestAccountConfirmationEmail(\App\Models\User::find(1), url('verify'));
                //return new \App\Mail\User\NotifyCoachParticipantAdded(\App\Models\User::first(), \App\Models\User::skip(1)->first());
                return new \App\Mail\User\NotifyResidentParticipantAdded(\App\Models\User::first(), \App\Models\User::skip(1)->first());
            });
        }

        Route::group([], base_path('routes/auth.php'));

        Route::view('styleguide', 'cooperation.frontend.styleguide');
        Route::view('input-guide', 'cooperation.frontend.input-guide');

        Route::get('/', function () {
            return view('cooperation.welcome');
        })->name('welcome');

        Route::get('switch-language/{locale}', [Cooperation\UserLanguageController::class, 'switchLanguage'])->name('switch-language');

        Route::name('recover-old-email.')->prefix('recover-old-email')->group(function () {
            Route::get('{token}', [Cooperation\RecoverOldEmailController::class, 'recover'])->name('recover');
        });

        Route::resource('privacy', Cooperation\PrivacyController::class)->only('index');
        Route::resource('disclaimer', Cooperation\DisclaimController::class)->only('index');

        // group can be accessed by everyone that's authorized and has a role in its session
        Route::middleware(['auth', 'current-role:resident|cooperation-admin|coordinator|coach|super-admin|superuser', 'verified'])->group(function () {
            Route::get('messages/count', [Cooperation\MessagesController::class, 'getTotalUnreadMessageCount'])->name('message.get-total-unread-message-count');

            if (in_array(app()->environment(), ['local', 'accept'])) {
                // debug purpose only
                Route::name('pdf.')->prefix('pdf')->group(function () {
                    Route::name('user-report.')->prefix('user-report')->group(function () {
                        Route::get('{scanShort?}', [Cooperation\Pdf\UserReportController::class, 'index'])->name('index');
                    });
                });
            }
            Route::get('home', [Cooperation\HomeController::class, 'index'])->name('home')->middleware('deny-if-filling-for-other-building');

            Route::prefix('file-storage')->name('file-storage.')->group(function () {
                Route::post('{fileType}', [Cooperation\FileStorageController::class, 'store'])
                    ->name('store');

                Route::get('download/{fileStorage}', [Cooperation\FileStorageController::class, 'download'])
                    ->name('download');
            });

            Route::get('input-source/{input_source_value_id}', [Cooperation\InputSourceController::class, 'changeInputSourceValue'])->name('input-source.change-input-source-value');

            Route::name('messages.')->prefix('messages')->group(function () {
                Route::name('participants.')->prefix('participants')->group(function () {
                    Route::post('revoke-access', [Cooperation\Messages\ParticipantController::class, 'revokeAccess'])->name('revoke-access');
                    Route::post('add-with-building-access', [Cooperation\Messages\ParticipantController::class, 'addWithBuildingAccess'])->name('add-with-building-access');
                });
            });

            // my account
            Route::name('my-account.')->prefix('my-account')->middleware('track-visited-url', 'deny-if-filling-for-other-building')->group(function () {
                Route::resource('two-factor-authentication', Cooperation\MyAccount\TwoFactorAuthenticationController::class);
                Route::get('', [Cooperation\MyAccount\MyAccountController::class, 'index'])->name('index');

                Route::prefix('settings')->name('settings.')->group(function () {
                    Route::put('', [Cooperation\MyAccount\SettingsController::class, 'update'])->name('update');
                    Route::delete('destroy', [Cooperation\MyAccount\SettingsController::class, 'destroy'])->name('destroy');
                    Route::post('reset-dossier', [Cooperation\MyAccount\SettingsController::class, 'resetFile'])->name('reset-file');
                });

                Route::resource('hoom-settings', Cooperation\MyAccount\HoomSettingsController::class)
                    ->only('update');

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

            Route::as('frontend.')->middleware(['track-visited-url'])->group(function () {
                Route::resource('help', Cooperation\Frontend\HelpController::class)->only('index');
                Route::as('tool.')->group(function () {
                    Route::get('{scan}', [Cooperation\Frontend\Tool\ScanController::class, 'redirect'])
                        ->name('scan.redirect')
                        ->whereIn('scan', \App\Models\Scan::allShorts());

                    Route::prefix('{scan}')
                        ->whereIn('scan', \App\Models\Scan::simpleShorts())
                        ->as('simple-scan.')
                        ->middleware('cooperation-has-scan')
                        ->group(function () {
                            Route::prefix('{step:slug}')
                                ->whereIn('step', \App\Models\Step::allSlugs())
                                ->group(function () {
                                    // Define this route as last to not match above routes as step/sub step combo
                                    Route::get('{subStep:slug}', [Cooperation\Frontend\Tool\SimpleScanController::class, 'index'])
                                        ->name('index')
                                        ->middleware(['checks-conditions-for-sub-steps', 'duplicate-data-for-user']);

                                    Route::get('vragenlijst/{questionnaire}', [Cooperation\Frontend\Tool\SimpleScan\QuestionnaireController::class, 'index'])
                                        ->name('questionnaires.index');
                                });

                            Route::as('my-plan.')->prefix('woonplan')->group(function () {
                                Route::get('', [Cooperation\Frontend\Tool\SimpleScan\MyPlanController::class, 'index'])->name('index');
                                Route::get('bestanden/{building?}', [Cooperation\Frontend\Tool\SimpleScan\MyPlanController::class, 'media'])->name('media');
                            });

                            Route::as('my-regulations.')->prefix('mijn-regelingen')->group(function () {
                                Route::get('', [Cooperation\Frontend\Tool\SimpleScan\MyRegulationsController::class, 'index'])->name('index');
                            });
                        });

                    //TODO: Bind by expert shorts and route bind steps also (perhaps we can merge with above code to
                    // minify route code...)
                    Route::as('expert-scan.')->prefix('expert-scan')->group(function () {
                        // Define this route as last to not match above routes as step/sub step combo
                        Route::prefix('{step}')
                            ->group(function () {
                                Route::get('', [Cooperation\Frontend\Tool\ExpertScanController::class, 'index'])
                                    ->name('index')
                                    ->middleware(['ensure-quick-scan-completed', 'duplicate-data-for-user']);

                                Route::get('vragenlijst/{questionnaire}',
                                    [Cooperation\Frontend\Tool\ExpertScan\QuestionnaireController::class, 'index'])
                                    ->name('questionnaires.index');
                            });
                    });
                });
            });

            Route::prefix('tool')->name('tool.')->middleware('ensure-quick-scan-completed', 'track-visited-url')->group(function () {
                Route::get('/', function () {
                    // Usually we check the scans. However, the lite scan can't come here anyway.
                    $scan = \App\Models\Scan::findByShort(\App\Models\Scan::QUICK);
                    return redirect()->route('cooperation.frontend.tool.simple-scan.my-plan.index', compact('scan'));
                })->name('index');

                Route::prefix('questionnaire')->name('questionnaire.')->group(function () {
                    Route::post('', [Cooperation\Tool\QuestionnaireController::class, 'store'])->name('store');
                });

                // TODO: Deprecate
                // Heat pump > Heating
                Route::get('heat-pump', function () {
                    Log::debug('HeatPumpController::index redirecting to heating');

                    return redirect()->route('cooperation.frontend.tool.expert-scan.index', ['step' => 'verwarming']);
                })->name('heat-pump.index');

                // HR boiler > Heating
                Route::prefix('high-efficiency-boiler')->name('high-efficiency-boiler.')->group(function () {
                    Route::get('', function () {
                        Log::debug('HighEfficiencyBoilerController::index redirecting to heating');

                        return redirect()->route('cooperation.frontend.tool.expert-scan.index', ['step' => 'verwarming']);
                    })->name('index');
                });

                // Heater (solar boiler) > Heating
                Route::prefix('heater')->name('heater.')->group(function () {
                    Route::get('', function () {
                        Log::debug('HeaterController::index redirecting to heating');

                        return redirect()->route('cooperation.frontend.tool.expert-scan.index', ['step' => 'verwarming']);
                    })->name('index');
                });
                // TODO: End deprecation

                // Ventilation
                Route::prefix('ventilation')->name('ventilation.')->group(function () {
                    Route::resource('', Cooperation\Tool\VentilationController::class)->only('index', 'store');
                    Route::post('calculate', [Cooperation\Tool\VentilationController::class, 'calculate'])->name('calculate');
                });

                // Wall Insulation
                Route::prefix('wall-insulation')->name('wall-insulation.')->group(function () {
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

                // Solar panels
                Route::prefix('solar-panels')->name('solar-panels.')->group(function () {
                    Route::resource('', Cooperation\Tool\SolarPanelsController::class)->only('index', 'store');
                    Route::post('calculate', [Cooperation\Tool\SolarPanelsController::class, 'calculate'])->name('calculate');
                });
            });

            Route::prefix('admin')->name('admin.')->middleware('role:cooperation-admin|coordinator|coach|super-admin|superuser', 'restore-building-session-if-filling-for-other-building')->group(function () {
                Route::get('/', [Cooperation\Admin\AdminController::class, 'index'])->name('index');
                Route::get('stop-session', [Cooperation\Admin\AdminController::class, 'stopSession'])
                    ->withoutMiddleware('restore-building-session-if-filling-for-other-building')
                    ->name('stop-session');
                Route::get('/switch-role/{role}', [Cooperation\Admin\SwitchRoleController::class, 'switchRole'])->name('switch-role');

                Route::prefix('roles')->name('roles.')->group(function () {
                    Route::post('assign-role', [Cooperation\Admin\RoleController::class, 'assignRole'])->name('assign-role');
                    Route::post('remove-role', [Cooperation\Admin\RoleController::class, 'removeRole'])->name('remove-role');
                });

                Route::middleware('current-role:cooperation-admin|super-admin')->group(function () {
                    Route::resource('example-buildings', Cooperation\Admin\ExampleBuildingController::class)
                        ->parameter('example-buildings', 'exampleBuilding')
                        ->only(['index', 'create', 'edit', 'destroy']);
                    Route::get('example-buildings/{exampleBuilding}/copy', [Cooperation\Admin\ExampleBuildingController::class, 'copy'])->name('example-buildings.copy');
                });

                /* Section that a coach, coordinator and cooperation-admin can access */
                Route::middleware('current-role:cooperation-admin|coach|coordinator')->group(function () {
                    Route::prefix('actions')->as('actions.')->group(function () {
                        Route::get('{account}/verify-email', [Cooperation\Admin\ActionController::class, 'verifyEmail'])->name('verify-email');
                    });

                    Route::resource('messages', Cooperation\Admin\MessagesController::class)->only('index');

                    Route::prefix('tool')->name('tool.')->group(function () {
                        Route::get('fill-for-user/{building}/{scan}', [Cooperation\Admin\ToolController::class, 'fillForUser'])->name('fill-for-user');
                        Route::get('observe-tool-for-user/{building}/{scan}', [Cooperation\Admin\ToolController::class, 'observeToolForUser'])
                            ->name('observe-tool-for-user');
                    });

                    Route::post('message', [Cooperation\Admin\MessagesController::class, 'sendMessage'])->name('send-message');

                    Route::resource('building-notes', Cooperation\Admin\BuildingNoteController::class)
                        ->only('store');

                    Route::prefix('building-status')->name('building-status.')->group(function () {
                        Route::post('set-status', [Cooperation\Admin\BuildingStatusController::class, 'setStatus'])->name('set-status');
                        Route::post('set-appointment-date',
                            [Cooperation\Admin\BuildingStatusController::class, 'setAppointmentDate'])->name('set-appointment-date');
                    });
                });

                Route::middleware('current-role:cooperation-admin|coach|coordinator|super-admin')->group(function () {
                    Route::name('buildings.')->prefix('buildings')->group(function () {
                        Route::get('show/{building}', [Cooperation\Admin\BuildingController::class, 'show'])->name('show');

                        Route::middleware('current-role:cooperation-admin|coordinator|super-admin')->group(function () {
                            Route::get('{building}/edit', [Cooperation\Admin\BuildingController::class, 'edit'])->name('edit');
                            Route::put('{building}', [Cooperation\Admin\BuildingController::class, 'update'])->name('update');
                        });
                    });
                });

                Route::resource('users', Cooperation\Admin\UserController::class)
                    ->only(['index', 'create', 'store'])
                    ->middleware('current-role:cooperation-admin|coordinator');
                Route::prefix('users')->name('users.')->middleware('current-role:cooperation-admin')->group(function () {
                    Route::delete('delete', [Cooperation\Admin\UserController::class, 'destroy'])->name('destroy');
                });

                /* Section for the cooperation-admin and coordinator */
                Route::prefix('cooperatie')->name('cooperation.')->middleware('current-role:cooperation-admin|coordinator')->group(function () {
                    Route::post('accounts/disable-2fa', [Cooperation\Admin\Cooperation\CooperationAdmin\AccountController::class, 'disableTwoFactorAuthentication'])
                        ->middleware('current-role:cooperation-admin')
                        ->name('accounts.disable-2fa');

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

                        Route::resource('scans', Cooperation\Admin\Cooperation\CooperationAdmin\ScanController::class)
                            ->only(['index', 'store']);

                        Route::resource('cooperation-measure-applications', CooperationMeasureApplicationController::class)
                            ->except(['index', 'create', 'store', 'show'])
                            ->parameter('cooperation-measure-applications', 'cooperationMeasureApplication');
                        Route::prefix('cooperation-measure-applications')->as('cooperation-measure-applications.')->group(function () {
                            // TODO: Deprecate to whereIn in L9
                            Route::prefix('{type}')
                                ->where(collect(['type'])
                                    ->mapWithKeys(fn ($parameter) => [
                                        $parameter => implode('|',
                                            \App\Helpers\Models\CooperationMeasureApplicationHelper::getMeasureTypes()),
                                    ])
                                    ->all()
                                )->group(function () {
                                    Route::get('', [CooperationMeasureApplicationController::class, 'index'])
                                        ->name('index');
                                    Route::get('create', [CooperationMeasureApplicationController::class, 'create'])
                                        ->name('create');
                                    Route::post('create', [CooperationMeasureApplicationController::class, 'store'])
                                        ->name('store');
                                });

                        });
                    });
                });

                /* Section for the super admin */
                Route::prefix('super-admin')->name('super-admin.')
                    ->middleware('current-role:super-admin')
                    ->group(base_path('routes/super-admin.php'));

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
    if (str_contains(\Illuminate\Support\Facades\Request::url(), '://www.')) {
        // The user has prefixed the subdomain with a www subdomain.
        // Remove the www part and redirect to that.
        return redirect(str_replace('://www.', '://', Request::url()));
    }

    return view('welcome');
})->name('index');
