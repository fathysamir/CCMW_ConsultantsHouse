<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Casts\CustomDateTimeCast;

class FileDocumentFlags extends Model
{
    use HasFactory;
    protected $table = 'flags';
    
    

    protected $fillable = [
        'flag',
        'user_id',
        'file_document_id'
    ];

    protected $allowedSorts = [
       
        'created_at',
        'updated_at'
    ];

    
}
