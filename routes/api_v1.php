<?php

use App\Http\Controllers\Api\V1\TaskController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


Route::get('/users', [UserController::class, 'index'])->name('users_index');
Route::post('/users/create', [UserController::class, 'create'])->name('create_user');
Route::get('/login', [UserController::class, 'getApiToken'])->name('login');

Route::middleware('auth:sanctum')->prefix('tasks')->group(function () {
    Route::get('/', [TaskController::class, 'index'])->name('tasks_index');
    Route::get('change-task', [TaskController::class, 'reReleaseTask'])->name('change_task');
    Route::post('done', [TaskController::class, 'done'])->name('done_task');
});
