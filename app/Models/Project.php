<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'projects';

    public $logoCollection = 'logo';
    public $perspectiveCollection = 'perspective';
    public $masterCollection = 'master';

    protected $fillable = [
        'code',
        'slug',
        'name',
        'account_id',
        'category_id',
        'contract_date',
        'commencement_date',
        'condation_contract',
        'original_value',
        'revised_value',
        'currency',
        'measurement_basis',
        'notes',
        'summary',
        'status',
        'user_id',
        'old_category_id',

    ];

    protected $allowedSorts = [

        'created_at',
        'updated_at',
    ];

    protected $hidden = ['deleted_at'];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id')->withTrashed();
    }

    public function old_category()
    {
        return $this->belongsTo(Category::class, 'old_category_id', 'id')->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id')->withTrashed();
    }

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id', 'id')->withTrashed();
    }

    public function stakeHolders()
    {
        return $this->hasMany(StakeHolder::class, 'project_id');
    }

    public function milestones()
    {
        return $this->hasMany(Milestone::class, 'project_id');
    }

    public function contractTags()
    {
        return $this->hasMany(ContractTag::class, 'project_id');
    }

    public function docTypes()
    {
        return $this->hasMany(DocType::class, 'project_id');
    }

    public function contractSettings()
    {
        return $this->hasMany(ContractSetting::class, 'project_id');
    }

    public function projectFolders()
    {
        return $this->hasMany(ProjectFolder::class, 'project_id');
    }

    public function Documents()
    {
        return $this->hasMany(Document::class, 'project_id');
    }

    public function assign_users()
    {
        return $this->belongsToMany(User::class, 'projects_users', 'project_id', 'user_id')->withPivot('permissions'); // Optional
    }
}
