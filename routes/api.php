<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\UserprofileController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\CollectionController;
use App\Http\Controllers\DocumentController;
use Illuminate\Routing\Events\RouteMatched;

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('sendCode', [ResetPasswordController::class, 'sendResetCode']);
Route::post('verifyCode', [ResetPasswordController::class, 'verifyResetCode']);
Route::post('resetPassword', [ResetPasswordController::class, 'resetPassword']);
Route::middleware('auth:api')->get('user', function (Request $request) {
    return $request->user();
});

//Profil utilisateur 

Route::middleware('auth:api')->group(function(){
    Route::get('user/profile',[UserprofileController::class, 'profile']);
    Route::patch('user/renameProfile',[UserprofileController::class, 'renameProfile']);
    Route::post('logout', [AuthController::class, 'logout']);
});


Route::middleware('auth:api')->group(function () {
    
    // Routes pour les collections
    Route::prefix('collections')->group(function () {
        Route::post('/ajouter', [CollectionController::class, 'store']);
        Route::get('/afficher', [CollectionController::class, 'index']);
        Route::delete('/supprimer/{id}', [CollectionController::class, 'destroy']);
        Route::patch('/modifier/{id}', [CollectionController::class, 'update']);
    });

    // Routes pour les documents
    Route::prefix('documents')->group(function () {
        Route::post('/ajouter', [DocumentController::class, 'store']);
        Route::get('/afficher', [DocumentController::class, 'index']);
        Route::get('/afficherCollection/{id}', [DocumentController::class, 'afficherCollection']);
        Route::get('/corbeille', [DocumentController::class, 'corbeille']);
        Route::put('/supprimer/{id}', [DocumentController::class, 'placerCorbeille']);
        Route::patch('/restaurer/{id}', [DocumentController::class, 'restaurer']);
        Route::delete('/supprimerDefinitivement/{id}', [DocumentController::class, 'destroy']);
        Route::patch('/modifier/{id}', [DocumentController::class, 'update']);
    });

    
});