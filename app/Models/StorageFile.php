<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StorageFile extends Model
{
    use HasFactory;

    protected $table = 'storage_files';

    protected $fillable = [
        'project_id',
        'user_id',
        'file_name',
        'size',
        'file_type',
        'path',
    ];

    protected $allowedSorts = [

        'created_at',
        'updated_at',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'id')->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id')->withTrashed();
    }
}
