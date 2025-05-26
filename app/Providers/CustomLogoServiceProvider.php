<?php

namespace App\Providers;

use App\Models\AccountUser;
use App\Models\ProjectFolder;
use App\Models\ProjectUser;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class CustomLogoServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('Folders', function () {
            // Logic to determine the path to the logo image
            // $logo=url(Setting::where('key','logo')->where('category','website')->where('type','file')->first()->value);
            $user = auth()->user();
            $Folders = ProjectFolder::where('account_id', $user->current_account_id)->where('project_id', $user->current_project_id);

            return $Folders;
        });

        $this->app->bind('Acco_Permissions', function () {
            $user = auth()->user();
            $AccountUser = AccountUser::where('account_id', $user->current_account_id)->where('user_id', $user->id)->first();
            if ($AccountUser) {
                $permissions = $AccountUser->permissions;
                $Acco_Permissions = json_decode($permissions);
            } else {
                $Acco_Permissions = null;
            }

            return $Acco_Permissions;
        });

        $this->app->bind('Project_Permissions', function () {
            $user = auth()->user();
            $ProjectUser = ProjectUser::where('project_id', $user->current_project_id)->where('user_id', $user->id)->first();
            if ($ProjectUser) {
                $permissions = $ProjectUser->permissions;
                $Pro_Permissions = json_decode($permissions);
            } else {
                $Pro_Permissions = null;
            }

            return $Pro_Permissions;
        });
    }

    public function boot()
    {
        view()->composer('*', function ($view) {
            $currentRoute = Route::currentRouteName();

            // Skip for login and register pages
            if (in_array($currentRoute, ['login_view', 'register_view'])) {
                return;
            }

            $user = auth()->user();

            // Avoid running for guest users
            if (! $user) {
                return;
            }
            $Folders = ProjectFolder::where('account_id', $user->current_account_id)
                ->where('project_id', $user->current_project_id)->orderBy('order')
                ->get();

            $view->with('Folders', $Folders);
        });
        view()->composer('*', function ($view) {
            $currentRoute = Route::currentRouteName();

            // Skip for login and register pages
            if (in_array($currentRoute, ['login_view', 'register_view'])) {
                return;
            }

            $user = auth()->user();

            // Avoid running for guest users
            if (! $user) {
                return;
            }
            $sideBarTheme = $user->sideBarTheme;

            $view->with('sideBarTheme', $sideBarTheme);
        });

        view()->composer('*', function ($view) {
            $currentRoute = Route::currentRouteName();

            // Skip for login and register pages
            if (in_array($currentRoute, ['login_view', 'register_view'])) {
                return;
            }

            $user = auth()->user();

            // Avoid running for guest users
            if (! $user) {
                return;
            }
            $AccountUser = AccountUser::where('account_id', $user->current_account_id)->where('user_id', $user->id)->first();

            $Account_Permissions = $AccountUser ? json_decode($AccountUser->permissions) : null;

            $view->with('Account_Permissions', $Account_Permissions);
        });
        view()->composer('*', function ($view) {
            // Get current route name
            $currentRoute = Route::currentRouteName();

            // Skip for login and register pages
            if (in_array($currentRoute, ['login_view', 'register_view'])) {
                return;
            }

            $user = auth()->user();

            // Avoid running for guest users
            if (! $user) {
                return;
            }

            $ProjectUser = ProjectUser::where('project_id', $user->current_project_id)
                ->where('user_id', $user->id)
                ->first();

            $Project_Permissions = $ProjectUser ? json_decode($ProjectUser->permissions) : null;

            $view->with('Project_Permissions', $Project_Permissions);
        });
    }
}
