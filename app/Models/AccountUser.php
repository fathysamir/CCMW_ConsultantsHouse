<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountUser extends Model
{
    use HasFactory;

    protected $table = 'accounts_users';

    protected $fillable = [
        'user_id',
        'account_id',
        'role',
        'permissions',

    ];

    protected $allowedSorts = [

        'created_at',
        'updated_at',
    ];
}
