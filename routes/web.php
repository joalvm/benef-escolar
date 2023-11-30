<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Views\AdminController;
use App\Http\Controllers\Views\UsersController;
use App\Http\Controllers\Views\PersonsController;
use App\Http\Controllers\Views\SessionController;
use App\Http\Controllers\Views\RegisterController;

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

Route::get('/', [RegisterController::class, 'index']);
Route::get('/register', [RegisterController::class, 'index']);
Route::get('/login', [SessionController::class, 'login']);
Route::post('/login', [SessionController::class, 'loginCheck']);
Route::get('/person/edit/{id}', [PersonsController::class, 'edit']);

// SE PUEDE RETROCEDER DESPUES DE HACER LOGOUT, CORREGUIR AGREFANDO AUTH PARA WEB
Route::get('/logout', [SessionController::class, 'logout']);

Route::prefix('user')
->middleware(['role:user'])
->group(function () {
    Route::get('formats', [UsersController::class, 'formats']);
    Route::get('bonds', [UsersController::class, 'bonds']);
    Route::get('bonds/children', [UsersController::class, 'bondsChildren']);
    Route::get('bonds/children/{id}', [UsersController::class, 'bondsChildrenEdit']);
    Route::get('children', [UsersController::class, 'children']);
});

Route::prefix('admin')
->middleware(['role:admin'])
->group(function () {
    Route::get('dashboard', [AdminController::class, 'dashboard']);
    Route::get('persons', [AdminController::class, 'persons']);
    Route::get('requests', [AdminController::class, 'requests']);
    Route::get('requests/approval/{id}', [AdminController::class, 'approval']);
    Route::get('requests/{id}/children/edit', [AdminController::class, 'bondsChildrenEdit']);

    Route::get(
        'persons/requests/excel',
        [ReportController::class, 'requestsExcel']
    );

    Route::get(
        'persons/requests/zip',
        [ReportController::class, 'requestsZip']
    );
});

Route::prefix('admin')
->middleware(['role:super_admin'])
->group(function () {
    Route::get('education_levels', [AdminController::class, 'educationLevels']);
    Route::get('periods', [AdminController::class, 'periods']);
});

Route::post('change-period', [AdminController::class, 'changePeriod'])
    ->middleware('role:admin,super_admin');

Route::get('/email-verification/{id}/{token}', [RegisterController::class, 'verification'])
->where([
    'id' => '^[0-9]+$',
    'token' => '^([A-Za-z0-9\-\_]*)\.([A-Za-z0-9\-\_]*)\.([A-Za-z0-9\-\_]*)$',
]);
