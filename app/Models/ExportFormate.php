<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExportFormate extends Model
{
    use HasFactory;

    protected $table = 'export_formate';

    protected $fillable = [
        'project_id',
        'account_id',
        'value'
    ];

    protected $allowedSorts = [

        'created_at',
        'updated_at',
    ];


    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'id')->withTrashed();
    }

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id', 'id')->withTrashed();
    }

   
}
