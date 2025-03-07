<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\ProjectFolder;
class CustomLogoServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('Folders', function () {
            // Logic to determine the path to the logo image
            //$logo=url(Setting::where('key','logo')->where('category','website')->where('type','file')->first()->value);
            $user=auth()->user();
            $Folders=ProjectFolder::where('account_id',$user->current_account_id)->where('project_id',$user->current_project_id);
            return $Folders;
        });
    }

    public function boot()
    {
        view()->composer('project_dashboard.layout.side_menu', function ($view) {
            $user = auth()->user();
            $Folders = ProjectFolder::where('account_id', $user->current_account_id)
                ->where('project_id', $user->current_project_id)
                ->get();
            
            $view->with('Folders', $Folders);
        });
        view()->composer('project_dashboard.layout.header', function ($view) {
            $user = auth()->user();
            $Folders = ProjectFolder::where('account_id', $user->current_account_id)
                ->where('project_id', $user->current_project_id)
                ->get();
            
            $view->with('Folders', $Folders);
        });
    }
}