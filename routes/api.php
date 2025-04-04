<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\UserController;
use Illuminate\Routing\Events\RouteMatched;

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::middleware('auth:api')->get('user', function (Request $request) {
    return $request->user();
});

//Profil utilisateur 

Route::middleware('auth:api')->group(function(){
    Route::get('user/profile',[UserController::class, 'profile']);
    Route::post('logout', [AuthController::class, 'logout']);
});


Route::middleware('auth:api')->group(function () {
    
    // Routes pour les abonnements
    Route::prefix('collections')->group(function () {
        Route::post('/ajouter', [CollectionController::class, 'store']);
        Route::get('/afficher', [CollectionController::class, 'index']);
        Route::delete('/supprimer/{id}', [CollectionController::class, 'destroy']);
        Route::patch('/modifier/{id}', [CollectionController::class, 'update']);
    });

    // Routes pour les collections
    Route::prefix('documents')->group(function () {
        Route::post('/ajouter', [DocumentController::class, 'store']);
        Route::get('/afficher', [DocumentController::class, 'index']);
        Route::delete('/supprimer/{id}', [DocumentController::class, 'destroy']);
        Route::patch('/modifier/{id}', [DocumentController::class, 'update']);
    });

    // Routes pour les préférences
    Route::prefix('preferences')->group(function () {
        Route::post('/ajouter', [DocumentController::class, 'store']);
        Route::get('/afficher', [DocumentController::class, 'index']);
        Route::delete('/supprimer/{id}', [DocumentController::class, 'destroy']);
        Route::patch('/modifier/{id}', [DocumentController::class, 'update']);
    });
});