<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\CourseController;

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
    public function boot()
    {
        View::composer(['admin.add-past-exams','admin.add-lecturer'], function ($view) {
            
                $courseController = new CourseController;
                $courseNames = $courseController->fetchCoursesForFaculty(); // This should return just the array
                $view->with('courseNames', $courseNames);
            
        });
    }

}
