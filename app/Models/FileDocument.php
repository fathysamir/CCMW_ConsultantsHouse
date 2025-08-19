<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FileDocument extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'file_documents';

    protected $fillable = [
        'file_id',
        'document_id',
        'note_id',
        'user_id',
        'sn',
        'narrative',
        'notes1',
        'notes2',
        'forClaim',
        'forLetter',
        'forChart',
        'ai_layer',

    ];

    protected $allowedSorts = [

        'created_at',
        'updated_at',
    ];

    public function document()
    {
        return $this->belongsTo(Document::class, 'document_id', 'id')->withTrashed();
    }

    public function note()
    {
        return $this->belongsTo(Note::class, 'note_id', 'id')->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id')->withTrashed();
    }

    public function file()
    {
        return $this->belongsTo(ProjectFile::class, 'file_id', 'id')->withTrashed();
    }

    public function tags()
    {
        return $this->belongsToMany(ContractTag::class, 'file_documents_tags', 'file_document_id', 'contract_tag_id');
    }

    public function gantt_chart()
    {
        return $this->hasOne(GanttChartDocData::class);
    }
}
