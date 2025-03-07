<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Casts\CustomDateTimeCast;
use Illuminate\Database\Eloquent\SoftDeletes;
class Milestone extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'milestones';
    
    

    protected $fillable = [
        'project_id',
        'name',
        'contract_finish_date',
        'revised_finish_date'
       
        
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
    
   
    
}
