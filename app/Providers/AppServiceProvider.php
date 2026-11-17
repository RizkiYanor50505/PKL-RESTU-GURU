<?php

namespace App\Providers;

use App\Models\PengajuanIzin;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
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
        View::composer(['absensi.layouts.app', 'absensi.layouts.bottom-nav'], function ($view): void {
            $view->with('pendingLeaveCount', session('auth_role') === 'manajer'
                ? PengajuanIzin::query()->where('status_izin', PengajuanIzin::STATUS_PENDING)->count()
                : 0);
        });
    }
}
