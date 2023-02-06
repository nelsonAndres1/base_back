<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ReportProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
        require_once app_path().'/Helpers/libs/UserReportPdf/UserReportPdf.php';
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
