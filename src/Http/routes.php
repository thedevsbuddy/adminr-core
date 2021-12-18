<?php

use Devsbuddy\AdminrCore\Http\Controllers\BuilderController;
use Devsbuddy\AdminrCore\Http\Controllers\ResourceController;
use Devsbuddy\AdminrCore\Http\Controllers\RolePermissionController;
use Illuminate\Support\Facades\Route;
//
Route::group(['prefix' => config('app.route_prefix'), 'middleware' => ['web'], 'as' => config('app.route_prefix').'.'], function(){

    Route::group(['middleware' => ['auth', 'admin']], function (){

        Route::get('/builder', [BuilderController::class, 'index'])->name('builder');
        Route::post('/generate', [BuilderController::class, 'build']);

        // Manage all resources
        Route::get('/resources', [ResourceController::class, 'index'])->name('resources.index');
        Route::delete('/resources/delete/{crud}', [ResourceController::class, 'destroy'])->name('resources.destroy');
        Route::get('/configure-resources/{id}', [ResourceController::class, 'configure'])->name('resources.configure');
        Route::get('/get-resource/{resource}', [ResourceController::class, 'getResource']);
        Route::post('/update-api-middleware/{resource}', [ResourceController::class, 'updateApiMiddlewares']);
        Route::get('/get-roles', [RolePermissionController::class, 'getRoles']);
        Route::get('/get-permissions/{resource}', [RolePermissionController::class, 'getPermissions']);
        Route::post('/sync-role-permissions', [RolePermissionController::class, 'assignPermissionsToRoles']);


    });

});
