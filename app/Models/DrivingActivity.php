<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DrivingActivity extends Model
{
    use HasFactory;

    protected $table = 'driving_activities';

    protected $fillable = [
        'project_id',
        'activity_id',
        'window_id',
        'program',
        'ms_come_date',
        'liability',
        'milestone_id',
        'file_id'
    ];

    protected $allowedSorts = [

        'created_at',
        'updated_at',
    ];

    

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'id')->withTrashed();
    }
     public function activity()
    {
        return $this->belongsTo(Activity::class, 'activity_id', 'id')->withTrashed();
    }
     public function window()
    {
        return $this->belongsTo(Window::class, 'window_id', 'id')->withTrashed();
    }
     public function milestone()
    {
        return $this->belongsTo(Milestone::class, 'milestone_id', 'id')->withTrashed();
    }
     public function file()
    {
        return $this->belongsTo(ProjectFile::class, 'file_id', 'id')->withTrashed();
    }

}
