<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectUser extends Model
{
    use HasFactory;

    protected $table = 'projects_users';

    protected $fillable = [
        'user_id',
        'account_id',
        'project_id',
        'permissions',
    ];

    protected $allowedSorts = [

        'created_at',
        'updated_at',
    ];
}
