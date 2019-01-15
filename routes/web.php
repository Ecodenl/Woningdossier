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

        // Logged In Section
        Route::group(['middleware' => 'auth'], function () {
            Route::get('home', 'HomeController@index')->name('home');
            Route::get('measures', 'MeasureController@index')->name('measures.index');
            Route::get('input-source/{input_source_value_id}', 'InputSourceController@changeInputSourceValue')->name('input-source.change-input-source-value');

            // my account
            Route::group(['as' => 'my-account.', 'prefix' => 'my-account', 'namespace' => 'MyAccount'], function () {
                Route::get('', 'MyAccountController@index')->name('index');
                Route::resource('settings', 'SettingsController', ['only' => ['index', 'store']]);
                Route::delete('settings', 'SettingsController@destroy')->name('settings.destroy');
                Route::post('settings/reset-dossier', 'SettingsController@resetFile')->name('settings.reset-file');

                Route::group(['as' => 'messages.', 'prefix' => 'messages', 'namespace' => 'Messages'], function () {
                    Route::get('', 'MessagesController@index')->name('index');
                    Route::get('edit/{mainMessageId}', 'MessagesController@edit')->name('edit');
                    Route::post('edit', 'MessagesController@store')->name('store');
                    Route::post('revoke-access', 'MessagesController@revokeAccess')->name('revoke-access');

                    Route::group(['prefix' => 'requests', 'as' => 'requests.'], function () {
                        Route::get('', 'RequestController@index')->name('index');
                        Route::get('{requestMessageId}', 'RequestController@edit')->name('edit');
                        Route::post('{requestMessageId}', 'RequestController@update')->name('update');
                    });
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
                Route::post('my-plan/store', 'MyPlanController@store')->name('my-plan.store');
                Route::get('my-plan/export', 'MyPlanController@export')->name('my-plan.export');
            });
        });

        // todo add admin middleware checking ACLs
        Route::group(['prefix' => 'admin', 'as' => 'admin.', 'namespace' => 'Admin', 'middleware' => ['role:cooperation-admin|coordinator|coach|super-admin|superuser']], function () {
            Route::get('/', 'AdminController@index')->name('index');
            Route::get('/switch-role/{role}', 'SwitchRoleController@switchRole')->name('switch-role');

            Route::group(['prefix' => 'cooperatie', 'as' => 'cooperation.', 'namespace' => 'Cooperation', 'middleware' => ['role:cooperation-admin|coordinator']], function () {
                Route::group(['prefix' => 'coordinator', 'as' => 'coordinator.', 'namespace' => 'Coordinator', 'middleware' => ['role:coordinator']], function () {
                    Route::group(['prefix' => 'reports', 'as' => 'reports.'], function () {
                        Route::get('', 'ReportController@index')->name('index');
                        Route::get('by-year', 'ReportController@downloadByYear')->name('download.by-year');
                        Route::get('by-measure', 'ReportController@downloadByMeasure')->name('download.by-measure');
                        Route::get('questionnaire-results', 'ReportController@downloadQuestionnaireResults')->name('download.questionnaire-results');
                    });

                    Route::group(['prefix' => 'users', 'as' => 'user.'], function () {
                        Route::get('', 'UserController@index')->name('index');
                        Route::get('create', 'UserController@create')->name('create');
                        Route::post('create', 'UserController@store')->name('store');
                        //Route::post('delete/{userId}', 'CoachController@destroy')->name('destroy');
                    });

                    Route::group(['prefix' => 'buildings', 'as' => 'building-access.'], function () {
                        Route::get('', 'BuildingAccessController@index')->name('index');
                        Route::get('{buildingId}', 'BuildingAccessController@edit')->name('edit');
                        Route::delete('destroy', 'BuildingAccessController@destroy')->name('destroy');
                    });

                    Route::group(['prefix' => 'reports', 'as' => 'reports.'], function () {
                        Route::get('', 'ReportController@index')->name('index');
                        Route::get('by-year', 'ReportController@downloadByYear')->name('download.by-year');
                        Route::get('by-measure', 'ReportController@downloadByMeasure')->name('download.by-measure');
                    });

                    Route::group(['prefix' => 'assign-roles', 'as' => 'assign-roles.'], function () {
                        Route::get('', 'AssignRoleController@index')->name('index');
                        Route::get('edit/{userId}', 'AssignRoleController@edit')->name('edit');
                        Route::post('edit/{userId}', 'AssignRoleController@update')->name('update');
                    });

                    Route::group(['as' => 'questionnaires.', 'prefix' => 'questionnaire'], function () {
                        Route::get('', 'QuestionnaireController@index')->name('index');
                        Route::post('', 'QuestionnaireController@update')->name('update');
                        Route::get('create', 'QuestionnaireController@create')->name('create');
                        Route::get('edit/{id}', 'QuestionnaireController@edit')->name('edit');
                        Route::post('create-questionnaire', 'QuestionnaireController@store')->name('store');

                        Route::delete('delete-question/{questionId}', 'QuestionnaireController@deleteQuestion')->name('delete');
                        Route::delete('delete-option/{questionId}/{optionId}', 'QuestionnaireController@deleteQuestionOption')->name('delete-question-option');
                        Route::post('set-active', 'QuestionnaireController@setActive')->name('set-active');
                    });

                    Route::group(['prefix' => 'conversation-requests', 'as' => 'conversation-requests.'], function () {
                        Route::get('', 'ConversationRequestsController@index')->name('index');
                        Route::get('request/{messageId}', 'ConversationRequestsController@show')->name('show');
                    });

                    Route::group(['prefix' => 'messages', 'as' => 'messages.'], function () {
                        Route::get('', 'MessagesController@index')->name('index');
                        Route::get('message/{messageId}', 'MessagesController@edit')->name('edit');
                        Route::post('message', 'MessagesController@store')->name('store');
                    });

                    Route::group(['prefix' => 'connect-to-coach', 'as' => 'connect-to-coach.'], function () {
                        Route::get('', 'ConnectToCoachController@index')->name('index');
                        Route::get('connect/{privateMessageId}', 'ConnectToCoachController@create')->name('create');
                        Route::get('consult-coach/{privateMessageId}', 'ConnectToCoachController@talkToCoachCreate')->name('talk-to-coach.create');
                        Route::post('consult-coach', 'ConnectToCoachController@talkToCoachStore')->name('talk-to-coach.store');
//                        Route::post('message-and-connect', 'ConnectToCoachController@storeWithMessageToCoach')->name('store-with-message-to-coach');
                        Route::post('', 'ConnectToCoachController@store')->name('store');
                    });

                    // needs to be the last route due to the param
                    Route::get('home', 'CoordinatorController@index')->name('index');
                });

                Route::group(['prefix' => 'cooperation-admin', 'as' => 'cooperation-admin.', 'namespace' => 'CooperationAdmin', 'middleware' => ['role:cooperation-admin|super-admin']], function () {
                    Route::group(['prefix' => 'assign-roles', 'as' => 'assign-roles.'], function () {
                        Route::get('', 'AssignRoleController@index')->name('index');
                        Route::get('edit/{userId}', 'AssignRoleController@edit')->name('edit');
                        Route::post('edit/{userId}', 'AssignRoleController@update')->name('update');
                    });

                    Route::group(['prefix' => 'users', 'as' => 'users.'], function () {
                        Route::get('', 'UserController@index')->name('index');
                        Route::get('create', 'UserController@create')->name('create');
                        Route::post('', 'UserController@store')->name('store');
                    });

                    Route::group(['prefix' => 'reports', 'as' => 'reports.'], function () {
                        Route::get('', 'ReportController@index')->name('index');
                        Route::get('by-year', 'ReportController@downloadByYear')->name('download.by-year');
                        Route::get('by-measure', 'ReportController@downloadByMeasure')->name('download.by-measure');
                    });

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

            Route::group(['prefix' => 'coach', 'as' => 'coach.', 'namespace' => 'Coach', 'middleware' => ['role:coach']], function () {
                Route::group(['prefix' => 'buildings', 'as' => 'buildings.'], function () {
                    Route::get('', 'BuildingController@index')->name('index');
                    Route::get('edit/{id}', 'BuildingController@edit')->name('edit');
                    Route::post('edit', 'BuildingController@update')->name('update');
                    Route::get('{id}', 'BuildingController@fillForUser')->name('fill-for-user');
                    Route::post('', 'BuildingController@setBuildingStatus')->name('set-building-status');

                    Route::group(['prefix' => 'details', 'as' => 'details.'], function () {
                        Route::get('{building_id}', 'BuildingDetailsController@index')->name('index');
                        Route::post('', 'BuildingDetailsController@store')->name('store');
                    });
                });

                Route::group(['prefix' => 'messages', 'as' => 'messages.'], function () {
                    Route::get('', 'MessagesController@index')->name('index');
                    Route::get('message/{messageId}', 'MessagesController@edit')->name('edit');
                    Route::post('message', 'MessagesController@store')->name('store');
                    Route::post('revoke-access', 'MessagesController@revokeAccess')->name('revoke-access');
                });

                Route::group(['prefix' => 'connect-to-resident', 'as' => 'connect-to-resident.'], function () {
                    Route::get('', 'ConnectToResidentController@index')->name('index');
                    Route::get('{userId}', 'ConnectToResidentController@create')->name('create');
                    Route::post('', 'ConnectToResidentController@store')->name('store');
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
