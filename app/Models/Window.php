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
    protected $appends = [
        'bas_snip',
        'frag_snip',
        'imp_snip',
        'upd_snip',
        'but_snip'
    ];
    public function getBasSnipAttribute()
    {
        return getFirstMediaUrl($this, $this->BASSnipCollection, true);
    }
     public function getFragSnipAttribute()
    {
        return getFirstMediaUrl($this, $this->FRAGSnipCollection, true);
    }
     public function getImpSnipAttribute()
    {
        return getFirstMediaUrl($this, $this->IMPSnipCollection, true);
    }
     public function getUpdSnipAttribute()
    {
        return getFirstMediaUrl($this, $this->UPDSnipCollection, true);
    }
     public function getButSnipAttribute()
    {
        return getFirstMediaUrl($this, $this->BUTSnipCollection, true);
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'id')->withTrashed();
    }
    public function drivings()
    {
        return $this->hasMany(DrivingActivity::class, 'window_id');
    }
}
