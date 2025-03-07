<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Casts\CustomDateTimeCast;
use Illuminate\Database\Eloquent\SoftDeletes;
class DocType extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'doc_types';

    protected $fillable = [
        'project_id',
        'account_id',
        'name',
        'order',
        'description'
    ];

    protected $allowedSorts = [
       
        'created_at',
        'updated_at'
    ];

    protected $hidden = ['deleted_at'];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id','id')->withTrashed();
    }

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id','id')->withTrashed();
    }

}
