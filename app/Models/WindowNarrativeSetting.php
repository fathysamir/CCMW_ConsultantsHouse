<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WindowNarrativeSetting extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'window_narrative_settings';

    protected $fillable = [
        'project_id',
        'account_id',
        'para_id',
        'description',
        'location',
        'paragraph',
        'paragraph_default'
        
    ];

    protected $allowedSorts = [
        'created_at',
        'updated_at',
    ];

    protected $hidden = ['deleted_at'];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'id')->withTrashed();
    }

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id', 'id')->withTrashed();
    }
}
