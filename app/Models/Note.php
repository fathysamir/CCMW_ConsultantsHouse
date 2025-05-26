<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Note extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'notes';

    protected $fillable = [

        'slug',
        'user_id',
        'project_id',
        'subject',
        'start_date',
        'end_date',
        'analysis_complete',
        'note',
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

    public function files()
    {
        return $this->belongsToMany(ProjectFile::class, 'file_documents', 'note_id', 'file_id');
    }
}
