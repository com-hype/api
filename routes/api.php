<?php

use App\Http\Controllers\{
    AuthenticationController,
    UserController,
};
use Illuminate\Support\Facades\Route;



Route::get('/health', function () {
    return response()->json(['status' => 'ok', 'version' => '1.0.0']);
});


Route::post('/auth/login', [AuthenticationController::class, 'login']);
Route::post('/auth/register', [AuthenticationController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post("/auth/register/finish", [UserController::class, 'finishRegister']);
    Route::get("/auth/me", [AuthenticationController::class, 'me']);
    Route::delete("/auth/logout", [AuthenticationController::class, 'logout']);
});
