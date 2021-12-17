<?php

use Devsbuddy\AdminrCore\Http\Controllers\BuilderController;
use Devsbuddy\AdminrCore\Http\Controllers\CrudController;
use Devsbuddy\AdminrCore\Http\Controllers\RolePermissionController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'adminr', 'middleware' => ['web'], 'as' => 'adminr.'], function(){

    Route::group(['middleware' => ['auth', 'admin']], function (){
        Route::get('/', [BuilderController::class, 'index'])->name('index');
        Route::get('/builder', [BuilderController::class, 'create'])->name('builder');
        Route::post('/generate', [BuilderController::class, 'build']);

        // Manage all cruds
        Route::get('/resources', [CrudController::class, 'index'])->name('cruds.index');
        Route::delete('/resources/delete/{crud}', [CrudController::class, 'destroy'])->name('cruds.destroy');
        Route::get('/configure-resources/{id}', [CrudController::class, 'configure'])->name('cruds.configure');
        Route::get('/get-resource/{resource}', [CrudController::class, 'getResource']);
        Route::post('/update-api-middleware/{resource}', [CrudController::class, 'updateApiMiddlewares']);
        Route::get('/get-roles', [RolePermissionController::class, 'getRoles']);
        Route::get('/get-permissions/{resource}', [RolePermissionController::class, 'getPermissions']);
        Route::post('/sync-role-permissions', [RolePermissionController::class, 'assignPermissionsToRoles']);


    });

});
