<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Casts\CustomDateTimeCast;
use Illuminate\Database\Eloquent\SoftDeletes;
class ContractTag extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'contract_tags';

    protected $fillable = [
        'project_id',
        'account_id',
        'name',
        'order',
        'description',
        'is_notice',
        'sub_clause',
        'var_process',
        'for_letter'
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
