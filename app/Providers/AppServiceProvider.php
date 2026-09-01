<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Leave;
use App\Models\MailRequest;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        View::composer('*', function ($view) {

            $pendingLeaves = Leave::with('employee')
    ->where('status', 'Pending')
    ->latest()
    ->get();

$pendingMails = MailRequest::with('employee')
    ->where('status', 'Pending')
    ->latest()
    ->get();

            $hasNotification = $pendingLeaves->count() || $pendingMails->count();

            $view->with(compact(
                'pendingLeaves',
                'pendingMails',
                'hasNotification'
            ));
        });
    }
}