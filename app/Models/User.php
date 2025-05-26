<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasRoles,Notifiable,SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    public $avatarCollection = 'avatar-image';

    protected $fillable = [
        'name',
        'code',
        'email',
        'phone',
        'current_account_id',
        'current_project_id',
        'current_folder_id',
        'password',
        'sideBarTheme',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function projects()
    {
        return $this->hasMany(Project::class, 'user_id');
    }

    public function accounts()
    {
        return $this->belongsToMany(Account::class, 'accounts_users', 'user_id', 'account_id')->withPivot('role', 'permissions');
    }

    public function assign_projects()
    {
        return $this->belongsToMany(Project::class, 'projects_users', 'user_id', 'project_id')->withPivot('permissions'); // Optional, if your pivot table has timestamps
    }

    // public function flags()
    // {
    //     return $this->belongsToMany(FileDocument::class,'flags', 'user_id', 'file_document_id')->withPivot('flag');
    // }
}
