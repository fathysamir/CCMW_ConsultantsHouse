<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Casts\CustomDateTimeCast;

class FileAttachmentFlag extends Model
{
    use HasFactory;
    protected $table = 'file_attachment_flags';
    
    

    protected $fillable = [
        'flag',
        'user_id',
        'file_attachment_id'
    ];

    protected $allowedSorts = [
       
        'created_at',
        'updated_at'
    ];

    
}
