<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StakeHolder extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'stake_holders';

    protected $fillable = [
        'project_id',
        'name',
        'role',
        'narrative',
        'article',

    ];

    protected $allowedSorts = [

        'created_at',
        'updated_at',
    ];

    protected $hidden = ['deleted_at'];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'id')->withTrashed();
    }
}
