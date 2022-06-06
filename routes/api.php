<?php

use App\Http\Controllers\{
    AuthenticationController,
    ImageController,
    UserController,
    ProjectController,
};
use Illuminate\Support\Facades\Route;



Route::get('health', function () {
    return response()->json(['status' => 'ok', 'version' => '1.0.0']);
});


Route::post('auth/login', [AuthenticationController::class, 'login']);
Route::post('auth/register', [AuthenticationController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post("auth/register/discoverer", [UserController::class, 'discovererRegistration']);
    Route::post("auth/register/presenter", [UserController::class, 'presenterRegistration']);

    Route::get("auth/me", [AuthenticationController::class, 'me']);
    Route::delete("auth/logout", [AuthenticationController::class, 'logout']);

    Route::get("projects", [ProjectController::class, 'index']);
    Route::post("projects/{project}/like", [ProjectController::class, 'like']);
    Route::post("upload/image", [ImageController::class, 'upload']);
});
