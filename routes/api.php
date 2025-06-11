<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\UserProcessingController;
use App\Http\Controllers\Api\V1\UserDataController;
use App\Http\Controllers\Api\V1\CpfStatusController;

Route::prefix('v1')->group(function () {
    Route::post('users/process', [UserProcessingController::class, 'process']);
    Route::get('users/{cpf}', [UserDataController::class, 'show']);
    Route::get('mock/cpf-status/{cpf}', [CpfStatusController::class, 'check'])
        ->name('api.mock.cpf-status');
});