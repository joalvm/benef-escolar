<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\FilesController;
use App\Http\Controllers\Api\UsersController;
use App\Http\Controllers\Api\PlantsController;
use App\Http\Controllers\Api\PeriodsController;
use App\Http\Controllers\Api\PersonsController;
use App\Http\Controllers\Api\RegisterController;
use App\Http\Controllers\Api\EducationLevelsController;
use App\Http\Controllers\Api\Ubigeo\DistrictsController;
use App\Http\Controllers\Api\Ubigeo\ProvincesController;
use App\Http\Controllers\Api\Children\ChildrenController;
use App\Http\Controllers\Api\Ubigeo\DepartmentsController;
use App\Http\Controllers\Api\Persons\RequestsController as PersonsRequestsController;
use App\Http\Controllers\Api\Children\RequestsController as ChildrenRequestController;
use App\Http\Controllers\Api\Persons\DocumentsController as PersonsDocumentsController;
use App\Http\Controllers\Api\Children\DocumentsController as ChildrenDocumentsController;

Route::get('/register/{dni}', [RegisterController::class, 'getDNI'])->where('dni', '[0-9]{8}');
Route::post('/register', [RegisterController::class, 'registerEmail']);

Route::apiResource('ubigeo/departments', DepartmentsController::class)->only('index', 'show');
Route::apiResource('ubigeo/provinces', ProvincesController::class)->only('index', 'show');
Route::apiResource('ubigeo/districts', DistrictsController::class)->only('index', 'show');

Route::middleware('auth')->group(function () {
    Route::apiResource('periods', PeriodsController::class);
    Route::apiResource('education_levels', EducationLevelsController::class);
    Route::apiResource('plants', PlantsController::class);
    Route::apiResource('children/requests/documents', ChildrenDocumentsController::class);
    Route::apiResource('children/requests', ChildrenRequestController::class);
    Route::apiResource('children', ChildrenController::class);

    Route::apiResource('persons/users', UsersController::class)->only(['show', 'update']);

    Route::get('persons/requests/counter', [PersonsRequestsController::class, 'getCounter']);

    Route::apiResource('persons/requests/documents', PersonsDocumentsController::class)->only(['update', 'store']);
    Route::apiResource('persons/requests', PersonsRequestsController::class);
    Route::apiResource('persons', PersonsController::class);

    Route::post(
        'children/notify/document',
        [ChildrenDocumentsController::class, 'sendNotificationEmail']
    );

    Route::post(
        'persons/notify/document',
        [PersonsDocumentsController::class, 'sendNotificationEmail']
    );

    Route::post('files', [FilesController::class, 'index']);
});
