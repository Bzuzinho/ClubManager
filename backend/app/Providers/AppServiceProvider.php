<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use App\Models\Membro;
use App\Models\Fatura;
use App\Policies\MembroPolicy;
use App\Policies\FaturaPolicy;

class AppServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Membro::class => MembroPolicy::class,
        Fatura::class => FaturaPolicy::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Desabilitar transações automáticas em migrations para PostgreSQL
        if (config('database.default') === 'pgsql') {
            \Illuminate\Support\Facades\Schema::defaultStringLength(191);
        }
        
        // Registar Policies
        foreach ($this->policies as $model => $policy) {
            Gate::policy($model, $policy);
        }
    }
}
