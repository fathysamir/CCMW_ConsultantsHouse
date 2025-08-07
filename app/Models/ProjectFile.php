<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectFile extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'project_files';

    protected $fillable = [
        'name',
        'slug',
        'code',
        'user_id',
        'project_id',
        'against_id',
        'start_date',
        'end_date',
        'folder_id',
        'older_folder_id',
        'notes',

        'time',
        'prolongation_cost',
        'disruption_cost',
        'variation',
        'closed',
        'assess_not_pursue',
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

    public function against()
    {
        return $this->belongsTo(StakeHolder::class, 'against_id', 'id')->withTrashed();
    }

    public function folder()
    {
        return $this->belongsTo(ProjectFolder::class, 'folder_id', 'id')->withTrashed();
    }

    public function older_folder()
    {
        return $this->belongsTo(ProjectFolder::class, 'older_folder_id', 'id')->withTrashed();
    }

    public function documents()
    {
        return $this->belongsToMany(Document::class, 'file_documents', 'file_id', 'document_id');
    }
    public function notes()
    {
        return $this->belongsToMany(Note::class, 'file_documents', 'file_id', 'note_id');
    }
    public function fileDocuments()
    {
        return $this->hasMany(FileDocument::class, 'file_id');
    }

     public function fileAttachment()
    {
        return $this->hasMany(FileAttachment::class, 'file_id');
    }

}
