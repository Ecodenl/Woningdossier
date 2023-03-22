<?php

use App\Http\Controllers\Cooperation;
use Illuminate\Support\Facades\Route;

Route::resource('clients', Cooperation\Admin\SuperAdmin\ClientController::class);

Route::resource('tool-questions', Cooperation\Admin\SuperAdmin\ToolQuestionController::class)
    ->parameter('tool-questions', 'toolQuestion')
    ->only(['index', 'edit', 'update']);

Route::resource('tool-calculation-results', Cooperation\Admin\SuperAdmin\ToolCalculationResultController::class)
    ->parameter('tool-calculation-results', 'toolCalculationResult')
    ->only(['index', 'edit', 'update']);

Route::resource('measure-categories', Cooperation\Admin\SuperAdmin\MeasureCategoryController::class)
    ->parameter('measure-categories', 'measureCategory')
    ->except(['show', 'destroy']);

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
    Route::get('create', [Cooperation\Admin\SuperAdmin\Cooperation\CooperationController::class, 'create'])->name('create');
    Route::post('', [Cooperation\Admin\SuperAdmin\Cooperation\CooperationController::class, 'store'])->name('store');
    Route::get('{cooperationToUpdate}/edit', [Cooperation\Admin\SuperAdmin\Cooperation\CooperationController::class, 'edit'])->name('edit');
    Route::put('{cooperationToUpdate}', [Cooperation\Admin\SuperAdmin\Cooperation\CooperationController::class, 'update'])->name('update');
    Route::delete('{cooperationToDestroy}', [Cooperation\Admin\SuperAdmin\Cooperation\CooperationController::class, 'destroy'])->name('destroy');

    /* Actions that will be done per cooperation */
    Route::prefix('{cooperationToManage}/')->name('cooperation-to-manage.')->group(function () {
        Route::resource('home', Cooperation\Admin\SuperAdmin\Cooperation\HomeController::class)
            ->only('index');

        Route::resource('cooperation-admin',
            Cooperation\Admin\SuperAdmin\Cooperation\CooperationAdminController::class)
            ->only(['index']);
        Route::resource('coordinator',
            Cooperation\Admin\SuperAdmin\Cooperation\CoordinatorController::class)
            ->only(['index']);
        Route::resource('users', Cooperation\Admin\SuperAdmin\Cooperation\UserController::class)
            ->only(['index', 'show', 'create', 'store']);
        Route::post('users/{id}/confirm', [
            Cooperation\Admin\SuperAdmin\Cooperation\UserController::class, 'confirm',])
            ->name('users.confirm');
    });
});

Route::resource('cooperation-presets', Cooperation\Admin\SuperAdmin\CooperationPresetController::class)
    ->only('index', 'show')
    ->parameter('cooperation-presets', 'cooperationPreset');

Route::resource('cooperation-presets.cooperation-preset-contents', Cooperation\Admin\SuperAdmin\CooperationPresetContentController::class)
    ->only('create', 'edit', 'destroy')
    ->parameters(['cooperation-presets' => 'cooperationPreset', 'cooperation-preset-contents' => 'cooperationPresetContent']);

Route::resource('municipalities', Cooperation\Admin\SuperAdmin\MunicipalityController::class)
    ->except('destroy');
Route::prefix('municipalities')->as('municipalities.')->group(function () {
    Route::put('{municipality}/couple', [Cooperation\Admin\SuperAdmin\MunicipalityController::class, 'couple'])->name('couple');
});
