<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Paragraph extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'paragraphs';

    protected $fillable = [
        'reply_user_id',///////////
        'slug',
        'number',
        'user_id',
        'title_above',
        'background_ref',
        'notes',
        'red_flag',
        'green_flag',
        'blue_flag',
        'replyed',/////////
        'background',
        'paragraph',
        'reply',
        'para_exhibits',
        'reply_exhibits',
        'para_numbers',
        'para_wise_id'

    ];

    protected $allowedSorts = [

        'created_at',
        'updated_at',
    ];

    public function para_wise()
    {
        return $this->belongsTo(ParaWise::class, 'para_wise_id', 'id')->withTrashed();
    }

    public function user_reply()
    {
        return $this->belongsTo(User::class, 'reply_user_id', 'id')->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id')->withTrashed();
    }
}
