<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', fn (Request $request) => $request->user());
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/profile', [ProfileController::class, 'edit']);
    Route::patch('/profile', [ProfileController::class, 'update']);

    Route::get('/leave-balance', [LeaveRequestController::class, 'balance']);
    Route::get('/leave-requests', [LeaveRequestController::class, 'index']);
    Route::post('/leave-requests', [LeaveRequestController::class, 'store']);
    Route::get('/team-requests', [LeaveRequestController::class, 'teamIndex']);
    Route::get('/ps-requests', [LeaveRequestController::class, 'psIndex']);
    Route::get('/leave-requests/{leaveRequest}', [LeaveRequestController::class, 'show']);
    Route::post('/leave-requests/{leaveRequest}/recommend', [LeaveRequestController::class, 'recommend']);
    Route::post('/leave-requests/{leaveRequest}/decide', [LeaveRequestController::class, 'decide']);
});
