<?php

use Illuminate\Support\Facades\Route;
use Modules\Task\Http\Controllers\TaskController;

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

Route::prefix('admin')->group(function () {
    Route::get('task', [TaskController::class, 'index'])
            ->middleware(['redirect.if.not.authenticated', 'can:view_tasks'])
            ->name('tasks.index');
    Route::post('task', [TaskController::class, 'store'])
            ->middleware(['redirect.if.not.authenticated', 'can:store_tasks'])
            ->name('tasks.store');
    Route::put('task/syncronize', [TaskController::class, 'syncronize'])
            ->middleware(['redirect.if.not.authenticated', 'can:sync_tasks'])
            ->name('tasks.sync');
    Route::put('task/{task}', [TaskController::class, 'update'])
            ->middleware(['redirect.if.not.authenticated', 'can:edit_tasks'])
            ->name('tasks.update');
});

Route::prefix('admin')->group(function () {
    Route::post('status', [TaskController::class, 'store'])
            ->middleware(['redirect.if.not.authenticated', 'can:store_status'])
            ->name('status.store');
    //Route::update('status', [TaskController::class, 'update'])
            //->middleware(['redirect.if.not.authenticated', 'can:update_status'])
            //->name('status.update');
});
