<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Casts\CustomDateTimeCast;
use Illuminate\Database\Eloquent\SoftDeletes;
class FileAttachment extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'file_attachments';

    protected $fillable = [
        'file_id',
        'user_id',
        'order',
        'narrative',
        'forClaim',
        'section'
    ];

    protected $allowedSorts = [
       
        'created_at',
        'updated_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id','id')->withTrashed();
    }
    public function file()
    {
        return $this->belongsTo(ProjectFile::class, 'file_id','id')->withTrashed();
    }
    
    

}
