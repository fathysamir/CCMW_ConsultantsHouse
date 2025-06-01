<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectAbbreviation extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'project_abbreviations';

    protected $fillable = [
        'project_id',
        'name',
        'role',
        'email',
        'phone',
        'country_code'
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
