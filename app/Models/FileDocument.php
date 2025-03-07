<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Casts\CustomDateTimeCast;
use Illuminate\Database\Eloquent\SoftDeletes;
class FileDocument extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'file_documents';

    protected $fillable = [
        'file_id',
        'document_id',
        'user_id',
        'sn',
        'narrative',
        'notes1',
        'notes2',
        'forClaim',
        'forLetter',
        'forChart'
        
    ];

    protected $allowedSorts = [
       
        'created_at',
        'updated_at'
    ];

  

    public function document()
    {
        return $this->belongsTo(Document::class, 'document_id','id')->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id','id')->withTrashed();
    }
    public function file()
    {
        return $this->belongsTo(ProjectFile::class, 'file_id','id')->withTrashed();
    }
   
    

}
