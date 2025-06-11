<?php

namespace App\Providers;

use App\Repositories\UserDataRepository;
use App\Services\CpfStatusService;
use App\Services\UserDataProcessingService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;


class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(CpfStatusService::class, function () {
            return new CpfStatusService();
        });
        
        $this->app->singleton(UserDataProcessingService::class, function ($app) {
            return new UserDataProcessingService(
                $app->make(UserDataRepository::class),
                $app->make(CpfStatusService::class)
            );
        });
    }

    public function boot(): void
    {
            Route::prefix('api')
             ->middleware(['api'])
             ->group(base_path('routes/api.php'));
    
    }
}