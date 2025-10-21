<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalculationMethod extends Model
{
    use HasFactory;

    protected $table = 'calculation_methods';

    protected $fillable = [
        'key',
        'project_id',
        'value',
       
    ];

    protected $allowedSorts = [

        'created_at',
        'updated_at',
    ];

    
    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'id')->withTrashed();
    }
}
