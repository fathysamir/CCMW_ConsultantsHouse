<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Window extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'windows';

    public $BASSnipCollection  = 'BASSnip';
    public $FRAGSnipCollection = 'FRAGSnip';
    public $IMPSnipCollection  = 'IMPSnip';
    public $UPDSnipCollection  = 'UPDSnip';
    public $BUTSnipCollection  = 'BUTSnip';

    protected $fillable = [
        'project_id',
        'slug',
        'no',
        'start_date',
        'end_date',
        'duration',
        'culpable',
        'excusable',
        'compensable',
        'transfer_compensable',
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
    public function drivings()
    {
        return $this->hasMany(DrivingActivity::class, 'window_id');
    }
}
