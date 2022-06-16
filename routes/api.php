<?php

use App\Http\Controllers\{
    AuthenticationController,
    CrowdfundingController,
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
    Route::get("projects/me", [ProjectController::class, 'get']);
    Route::get("projects/me/stats", [ProjectController::class, 'getStats']);
    Route::get("projects/{project}", [ProjectController::class, 'getProject']);
    Route::get("projects/{project}/crowdfunding", [ProjectController::class, 'getCrowdfunding']);
    Route::post("projects/{project}/like", [ProjectController::class, 'like']);
    Route::get("projects/{project}/features", [ProjectController::class, 'getFeatures']);
    Route::post("projects/{project}/features", [ProjectController::class, 'editFeatures']);
    Route::post("upload/image", [ImageController::class, 'upload']);
    Route::post("upload/image/{image}", [ImageController::class, 'replace']);

    Route::post("payment/intent", [CrowdfundingController::class, 'intent']);
});



// WEBHOOKS
Route::post("payment/webhook", [CrowdfundingController::class, 'webhook']);
