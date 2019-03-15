<?php

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

Route::domain('{cooperation}.'.config('woningdossier.domain'))->group(function () {
    Route::group(['middleware' => 'cooperation', 'as' => 'cooperation.', 'namespace' => 'Cooperation'], function () {
        Route::get('/', function () {
            return view('cooperation.welcome');
        }
        )->name('welcome');

        Route::get('switch-language/{locale}', 'UserLanguageController@switchLanguage')->name('switch-language');
        Route::get('confirm',
            'Auth\RegisterController@confirm')->name('confirm');
        Route::get('check-existing-mail', 'Auth\RegisterController@checkExistingEmail')->name('check-existing-email');
        Route::post('connect-existing-account', 'Auth\RegisterController@connectExistingAccount')->name('connect-existing-account');

        Route::get('fill-address', 'Auth\RegisterController@fillAddress')->name('fill-address');
        //		 Login, forgot password etc.

        Route::get('resend-confirm-account-email', 'Auth\RegisterController@formResendConfirmMail')->name('auth.form-resend-confirm-mail');
        Route::post('resend-confirm-account-email', 'Auth\RegisterController@resendConfirmMail')->name('auth.resend-confirm-mail');

        Auth::routes();

        Route::group(['prefix' => 'create-building', 'as' => 'create-building.'], function () {
            Route::get('', 'CreateBuildingController@index')->name('index');
            Route::post('', 'CreateBuildingController@store')->name('store');
        });

        // Logged In Section
        Route::group(['middleware' => 'auth'], function () {
            Route::get('home', 'HomeController@index')->name('home');
            //Route::get('measures', 'MeasureController@index')->name('measures.index');
            Route::get('input-source/{input_source_value_id}', 'InputSourceController@changeInputSourceValue')->name('input-source.change-input-source-value');

            Route::group(['as' => 'messages.', 'prefix' => 'messages', 'namespace' => 'Messages'], function () {
                Route::group(['as' => 'participants.', 'prefix' => 'participants'], function () {
                    Route::post('revoke-access', 'ParticipantController@revokeAccess')->name('revoke-access');
                    Route::post('add-with-building-access', 'ParticipantController@addWithBuildingAccess')->name('add-with-building-access');
                });
            });

            // my account
            Route::group(['as' => 'my-account.', 'prefix' => 'my-account', 'namespace' => 'MyAccount'], function () {
                Route::get('', 'MyAccountController@index')->name('index');
                Route::resource('settings', 'SettingsController', ['only' => ['index', 'store']]);
                Route::delete('settings', 'SettingsController@destroy')->name('settings.destroy');
                Route::post('settings/reset-dossier', 'SettingsController@resetFile')->name('settings.reset-file');

                Route::group(['as' => 'import-center.', 'prefix' => 'import-centrum'], function () {
                    Route::get('', 'ImportCenterController@index')->name('index');
                    Route::get('set-compare-session/{inputSourceShort}', 'ImportCenterController@setCompareSession')->name('set-compare-session');
                    Route::post('dismiss-notification', 'ImportCenterController@dismissNotification')->name('dismiss-notification');
                });
                Route::group(['as' => 'messages.', 'prefix' => 'messages', 'namespace' => 'Messages'], function () {
                    Route::get('', 'MessagesController@index')->name('index');
                    Route::get('edit', 'MessagesController@edit')->name('edit');
                    Route::post('edit', 'MessagesController@store')->name('store');
                    Route::post('revoke-access', 'MessagesController@revokeAccess')->name('revoke-access');
                });

                Route::group(['as' => 'access.', 'prefix' => 'access'], function () {
                    Route::get('', 'AccessController@index')->name('index');
                    Route::post('revoke-access', 'AccessController@revokeAccess')->name('revoke-access');
                });

                //Route::get('cooperations', 'CooperationsController@index')->name('cooperations.index');
            });

            // conversation requests
            Route::group(['prefix' => 'request', 'as' => 'conversation-requests.', 'namespace' => 'ConversationRequest'], function () {
                Route::get('/edit/{action?}', 'ConversationRequestController@edit')->name('edit');
                Route::get('{action?}/{measureApplicationShort?}', 'ConversationRequestController@index')->name('index');

                Route::post('', 'ConversationRequestController@store')->name('store');
                Route::post('/edit', 'ConversationRequestController@update')->name('update');

                //			    Route::group(['prefix' => 'coachgresprek', 'as' => 'coach.'], function () {
//			        Route::resource('', 'CoachController');
//                });

//			    Route::group(['prefix' => 'meer-informatie', 'as' => 'more-information.'], function () {
//
//			        Route::get('{measure}', 'MoreInfoController@index')->name('index');
//			        Route::post('', 'MoreInfoController@store')->name('store');
//                });
//
//			    Route::group(['prefix' => 'offerte', 'as' => 'quotation.'], function () {
//                    Route::get('{measure}', 'QuotationController@index')->name('index');
//                    Route::post('', 'QuotationController@store')->name('store');
//                });
            });

            // the tool
            Route::group(['prefix' => 'import', 'as' => 'import.'], function () {
                Route::post('', 'ImportController@copy')->name('copy');
            });
            Route::group(['prefix' => 'tool', 'as' => 'tool.', 'namespace' => 'Tool'], function () {
                Route::get('/', 'ToolController@index')->name('index');

                // todo
//                Route::get('general-data/example-building-type', 'GeneralDataController@exampleBuildingType')->name('general-data.example-building-type');
                // todo end
                Route::post('general-data/apply-example-building', 'GeneralDataController@applyExampleBuilding')->name('apply-example-building');
                Route::resource('building-detail', 'BuildingDetailController', ['only' => ['index', 'store']]);

                Route::group(['prefix' => 'questionnaire', 'as' => 'questionnaire.'], function () {
                    Route::post('', 'QuestionnaireController@store')->name('store');
                });

                Route::group(['middleware' => 'filled-step:building-detail'], function () {
                    Route::resource('general-data', 'GeneralDataController', ['only' => ['index', 'store']]);
                });
                Route::group(['middleware' => 'filled-step:general-data'], function () {
                    // Extra pages with downloadable or information content.
                    Route::group(['namespace' => 'Information'], function () {
                        Route::resource('ventilation-information', 'VentilationController', ['only' => ['index', 'store']]);
                    });

                    Route::resource('heat-pump', 'HeatPumpController', ['only' => ['index', 'store']]);

                    // Wall Insulation
                    Route::resource('wall-insulation', 'WallInsulationController', ['only' => ['index', 'store']]);
                    Route::post('wall-insulation/calculate', 'WallInsulationController@calculate')->name('wall-insulation.calculate');

                    // Insulated glazing
                    Route::resource('insulated-glazing', 'InsulatedGlazingController', ['only' => ['index', 'store']]);
                    Route::post('insulated-glazing/calculate', 'InsulatedGlazingController@calculate')->name('insulated-glazing.calculate');

                    // Floor Insulation
                    Route::resource('floor-insulation', 'FloorInsulationController', ['only' => ['index', 'store']]);
                    Route::post('floor-insulation/calculate', 'FloorInsulationController@calculate')->name('floor-insulation.calculate');

                    // Roof Insulation
                    Route::resource('roof-insulation', 'RoofInsulationController');
                    Route::post('roof-insulation/calculate', 'RoofInsulationController@calculate')->name('roof-insulation.calculate');

                    // HR boiler
                    Route::resource('high-efficiency-boiler', 'HighEfficiencyBoilerController', ['only' => ['index', 'store']]);
                    Route::post('high-efficiency-boiler/calculate', 'HighEfficiencyBoilerController@calculate')->name('high-efficiency-boiler.calculate');

                    // Solar panels
                    Route::resource('solar-panels', 'SolarPanelsController', ['only' => ['index', 'store']]);
                    Route::post('solar-panels/calculate', 'SolarPanelsController@calculate')->name('solar-panels.calculate');

                    // Heater (solar boiler)
                    Route::resource('heater', 'HeaterController', ['only' => ['index', 'store']]);
                    Route::post('heater/calculate', 'HeaterController@calculate')->name('heater.calculate');
                });

                Route::get('my-plan', 'MyPlanController@index')->name('my-plan.index');
                Route::post('my-plan/comment', 'MyPlanController@storeComment')->name('my-plan.store-comment');
                Route::post('my-plan/store', 'MyPlanController@store')->name('my-plan.store');
                Route::get('my-plan/export', 'MyPlanController@export')->name('my-plan.export');
            });
        });

        // todo add admin middleware checking ACLs
        Route::group(['prefix' => 'admin', 'as' => 'admin.', 'namespace' => 'Admin', 'middleware' => ['role:cooperation-admin|coordinator|coach|super-admin|superuser']], function () {
            Route::get('/', 'AdminController@index')->name('index');
            Route::get('/switch-role/{role}', 'SwitchRoleController@switchRole')->name('switch-role');

            Route::group(['prefix' => 'roles', 'as' => 'roles.'], function () {
                Route::post('assign-role', 'RoleController@assignRole')->name('assign-role');
                Route::post('remove-role', 'RoleController@removeRole')->name('remove-role');
            });

            Route::group(['prefix' => 'building-coach-status', 'as' => 'building-coach-status.'], function () {
                Route::post('set-status', 'BuildingCoachStatusController@setStatus')->name('set-status');
                Route::post('set-appointment-date', 'BuildingCoachStatusController@setAppointmentDate')->name('set-appointment-date');
            });
            Route::group(['middleware' => ['role:cooperation-admin|super-admin']], function () {
                Route::resource('example-buildings', 'ExampleBuildingController');
                Route::get('example-buildings/{id}/copy', 'ExampleBuildingController@copy')->name('example-buildings.copy');
            });

            Route::group(['prefix' => 'tool', 'as' => 'tool.'], function () {
                Route::get('fill-for-user/{id}', 'ToolController@fillForUser')->name('fill-for-user');
                Route::get('observe-tool-for-user/{id}', 'ToolController@observeToolForUser')->name('observe-tool-for-user');
            });

            Route::post('message', 'MessagesController@sendMessage')->name('send-message');

            Route::get('users/show/{id}', 'UserController@show')->name('users.show');

            /* Section for the cooperation-admin and coordinator */
            Route::group(['prefix' => 'cooperatie', 'as' => 'cooperation.', 'namespace' => 'Cooperation', 'middleware' => ['role:cooperation-admin|coordinator']], function () {
                Route::resource('example-buildings', 'ExampleBuildingController');
                Route::get('example-buildings/{id}/copy', 'ExampleBuildingController@copy')->name('example-buildings.copy');

                Route::group(['prefix' => 'users', 'as' => 'users.'], function () {
                    Route::get('', 'UserController@index')->name('index');
                    Route::get('show/{id}', 'UserController@show')->name('show');
                    Route::get('create', 'UserController@create')->name('create');
                    Route::post('create', 'UserController@store')->name('store');

                    Route::group(['middleware' => 'role:cooperation-admin'], function () {
                        Route::delete('delete', 'UserController@destroy')->name('destroy');
                    });
                });

                Route::resource('coaches', 'CoachController')->only(['index', 'show']);

                Route::group(['prefix' => 'reports', 'as' => 'reports.'], function () {
                    Route::get('', 'ReportController@index')->name('index');
                    Route::get('by-year', 'ReportController@downloadByYear')->name('download.by-year');
                    Route::get('by-measure', 'ReportController@downloadByMeasure')->name('download.by-measure');
                    Route::group(['middleware' => 'role:cooperation-admin'], function () {
                        Route::get('questionnaire-results', 'ReportController@downloadQuestionnaireResults')->name('download.questionnaire-results');
                    });
                });

                // not in the cooperation-admin group, probably need to be used for hte coordinator aswell.
                Route::group(['as' => 'questionnaires.', 'prefix' => 'questionnaire', 'middleware' => ['role:cooperation-admin']], function () {
                    Route::get('', 'QuestionnaireController@index')->name('index');
                    Route::post('', 'QuestionnaireController@update')->name('update');
                    Route::get('create', 'QuestionnaireController@create')->name('create');
                    Route::get('edit/{id}', 'QuestionnaireController@edit')->name('edit');
                    Route::post('create-questionnaire', 'QuestionnaireController@store')->name('store');

                    Route::delete('delete-question/{questionId}', 'QuestionnaireController@deleteQuestion')->name('delete');
                    Route::delete('delete-option/{questionId}/{optionId}', 'QuestionnaireController@deleteQuestionOption')->name('delete-question-option');
                    Route::post('set-active', 'QuestionnaireController@setActive')->name('set-active');
                });


                /* Section for the coordinator */
                Route::group(['prefix' => 'coordinator', 'as' => 'coordinator.', 'namespace' => 'Coordinator', 'middleware' => ['role:coordinator']], function () {

                    // needs to be the last route due to the param
                    Route::get('home', 'CoordinatorController@index')->name('index');
                });

                /* section for the cooperation-admin */
                Route::group(['prefix' => 'cooperation-admin', 'as' => 'cooperation-admin.', 'namespace' => 'CooperationAdmin', 'middleware' => ['role:cooperation-admin|super-admin']], function () {

                    Route::group(['prefix' => 'steps', 'as' => 'steps.'], function () {
                        Route::get('', 'StepController@index')->name('index');
                        Route::post('set-active', 'StepController@setActive')->name('set-active');
                    });

                    Route::resource('example-buildings', 'ExampleBuildingController');
                    Route::get('example-buildings/{id}/copy', 'ExampleBuildingController@copy')->name('example-buildings.copy');

                    // needs to be the last route due to the param
                    Route::get('home', 'CooperationAdminController@index')->name('index');
                });
            });

            /* Section for the super admin */
            Route::group(['prefix' => 'super-admin', 'as' => 'super-admin.', 'namespace' => 'SuperAdmin', 'middleware' => ['role:super-admin']], function () {
                Route::get('home', 'SuperAdminController@index')->name('index');

                Route::group(['prefix' => 'cooperations', 'as' => 'cooperations.'], function () {
                    Route::get('', 'CooperationController@index')->name('index');
                    Route::get('edit/{cooperationId}', 'CooperationController@edit')->name('edit');
                    Route::get('create', 'CooperationController@create')->name('create');
                    Route::post('', 'CooperationController@store')->name('store');
                    Route::post('edit', 'CooperationController@update')->name('update');
                });
                Route::resource('translations', 'TranslationController')->except(['show'])->parameters(['id' => 'step-slug']);
            });

            /* Section for the coach */
            Route::group(['prefix' => 'coach', 'as' => 'coach.', 'namespace' => 'Coach', 'middleware' => ['role:coach']], function () {


                Route::group(['prefix' => 'buildings', 'as' => 'buildings.'], function () {
                    Route::get('', 'BuildingController@index')->name('index');
                    Route::get('show/{id}', 'BuildingController@show')->name('show');
                    Route::get('edit/{id}', 'BuildingController@edit')->name('edit');
                    Route::post('edit', 'BuildingController@update')->name('update');
                    Route::post('', 'BuildingController@setBuildingStatus')->name('set-building-status');

                    Route::resource('details', 'BuildingDetailsController')->only('store');
                });

                // needs to be the last route due to the param
                Route::get('home', 'CoachController@index')->name('index');
            });

            // auth
            Route::group(['namespace' => 'Auth'], function () {
                Route::get('login', 'LoginController@showLoginForm')->name('login');
                Route::post('login', 'LoginController@login');
                Route::post('logout', 'LoginController@logout')->name('logout');
            });
        });
    });
});

Route::post('logout', 'Cooperation\Admin\Auth\LoginController@logout')->name('logout');
//Route::get('password/reset/{token?}', 'Cooperation\Auth\ResetPasswordController@showResetForm')->name('password.reset');
//Route::post('password/email', 'Cooperation\Auth\PasswordController@sendResetLinkEmail');
//Route::post('password/reset', 'Cooperation\Auth\PasswordController@reset');

Route::get('/', function () {
    return view('welcome');
})->name('index');
