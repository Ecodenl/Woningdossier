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

        Route::get('fill-address', 'Auth\RegisterController@fillAddress')->name('fill-address');
        // Login, forgot password etc.
        Auth::routes();


		// Logged In Section
		Route::group(['middleware' => 'auth'], function(){
			Route::get( 'home', 'HomeController@index' )->name( 'home' );
			Route::get('help', 'HelpController@index')->name('help.index');
//			Route::get('help-met-invullen', '')
			Route::get('measures', 'MeasureController@index')->name('measures.index');

			// my account
			Route::group(['as' => 'my-account.', 'prefix' => 'my-account', 'namespace' => 'MyAccount'], function() {

			    Route::get('', 'MyAccountController@index')->name('index');
				Route::resource('settings', 'SettingsController', ['only' => ['index', 'store', ]]);
				Route::delete('settings', 'SettingsController@destroy')->name('settings.destroy');
                Route::post('settings/reset-dossier', 'SettingsController@resetFile')->name('settings.reset-file');

				Route::group(['as' => 'messages.', 'prefix' => 'messages', 'namespace' => 'Messages'], function () {

				    Route::get('', 'MessagesController@index')->name('index');
				    Route::get('edit/{mainMessageId}', 'MessagesController@edit')->name('edit');
				    Route::post('edit', 'MessagesController@store')->name('store');

				    Route::group(['prefix' => 'aanvragen', 'as' => 'requests.'], function () {

				        Route::get('', 'RequestController@index')->name('index');
				        Route::get('{requestMessageId}', 'RequestController@edit')->name('edit');
				        Route::post('{requestMessageId}', 'RequestController@update')->name('update');
                    });
                });

				//Route::get('cooperations', 'CooperationsController@index')->name('cooperations.index');
			});

			// conversation requests
			Route::group(['prefix' => 'aanvragen', 'as' => 'conversation-requests.', 'namespace' => 'ConversationRequest'], function () {

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
            Route::group(['prefix' => 'tool', 'as' => 'tool.', 'namespace' => 'Tool'], function () {
            	Route::get('/', 'ToolController@index')->name('index');
                Route::resource('general-data', 'GeneralDataController', ['only' => ['index', 'store']]);

                Route::group(['middleware' => 'filled-step:general-data'], function(){

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
        Route::group(['prefix' => 'admin', 'as' => 'admin.', 'namespace' => 'Admin'], function () {
            Route::group(['namespace' => 'Auth'], function () {
                Route::get('login', 'LoginController@showLoginForm')->name('login');
                Route::post('login', 'LoginController@login');
                Route::post('logout', 'LoginController@logout')->name('logout');
            });

            // Logged In Section
            Route::group(['middleware' => ['auth', 'is-admin']], function () {
                Route::get('/', 'AdminController@index')->name('index');

                Route::group(['prefix' => 'reports', 'as' => 'reports.'], function () {
                    Route::get('', 'ReportController@index')->name('index');
                    Route::get('by-year', 'ReportController@downloadByYear')->name('download.by-year');
                    Route::get('by-measure', 'ReportController@downloadByMeasure')->name('download.by-measure');
                });

				Route::resource('example-buildings', 'ExampleBuildingController');
				Route::get('example-buildings/{id}/copy', 'ExampleBuildingController@copy')->name('example-buildings.copy');
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