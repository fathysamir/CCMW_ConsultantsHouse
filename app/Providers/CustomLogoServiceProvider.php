<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\ProjectFolder;
use App\Models\AccountUser;
use App\Models\ProjectUser;
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

        $this->app->bind('Acco_Permissions', function () {
            $user=auth()->user();
            $AccountUser=AccountUser::where('account_id',$user->current_account_id)->where('user_id',$user->id)->first();
            if($AccountUser){
                $permissions=$AccountUser->permissions;
                $Acco_Permissions=json_decode($permissions);
            }else{
                $Acco_Permissions=null;
            }
            
            return $Acco_Permissions;
        });

        $this->app->bind('Project_Permissions', function () {
            $user=auth()->user();
            $ProjectUser=ProjectUser::where('project_id',$user->current_project_id)->where('user_id',$user->id)->first();
            if($ProjectUser){
                $permissions=$ProjectUser->permissions;
                $Pro_Permissions=json_decode($permissions);
            }else{
                $Pro_Permissions=null;
            }
            
            return $Pro_Permissions;
        });
    }

    public function boot()
    {
        view()->composer('*', function ($view) {
            $user = auth()->user();
            $Folders = ProjectFolder::where('account_id', $user->current_account_id)
                ->where('project_id', $user->current_project_id)
                ->get();
            
            $view->with('Folders', $Folders);
        });
        
        view()->composer('*', function ($view) {
            $user=auth()->user();
            $AccountUser=AccountUser::where('account_id',$user->current_account_id)->where('user_id',$user->id)->first();
            if($AccountUser){
                $permissions=$AccountUser->permissions;
                $Account_Permissions=json_decode($permissions);
            }else{
                $Account_Permissions=null;
            }
            
            
            $view->with('Account_Permissions', $Account_Permissions);
        });
        view()->composer('*', function ($view) {
            $user=auth()->user();
            $ProjectUser=ProjectUser::where('project_id',$user->current_project_id)->where('user_id',$user->id)->first();
            if($ProjectUser){
                $permissions=$ProjectUser->permissions;
                $Project_Permissions=json_decode($permissions);
            }else{
                $Project_Permissions=null;
            }
            
            
            $view->with('Project_Permissions', $Project_Permissions);
        });
    }
}