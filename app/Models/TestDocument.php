<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Casts\CustomDateTimeCast;
class TestDocument extends Model
{
    use HasFactory;
    protected $table = 'test_documents';

    protected $fillable = [
        'doc_type_id',
        'user_id',
        'project_id',
        'subject',
        'start_date',
        'end_date',
        'from_id',
        'to_id',
        'reference',
        'revision',
        'status',
        'notes',
        'storage_file_id',
        'analyzed',
        'threads',
        'analysis_complete',
        'confirmed',
        'file_id'
    ];

    protected $allowedSorts = [
       
        'created_at',
        'updated_at'
    ];

  

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id','id')->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id','id')->withTrashed();
    }
    public function docType()
    {
        return $this->belongsTo(DocType::class, 'doc_type_id','id')->withTrashed();
    }
    public function fromStakeHolder()
    {
        return $this->belongsTo(StakeHolder::class, 'from_id','id')->withTrashed();
    }
    public function toStakeHolder()
    {
        return $this->belongsTo(StakeHolder::class, 'to_id','id')->withTrashed();
    }

    public function storageFile()
    {
        return $this->belongsTo(StorageFile::class, 'storage_file_id','id');
    }



}
