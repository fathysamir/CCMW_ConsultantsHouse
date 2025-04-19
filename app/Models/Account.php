<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Casts\CustomDateTimeCast;
use Illuminate\Database\Eloquent\SoftDeletes;
class Account extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'accounts';
    
    public $logoCollection = 'logo';
    

    protected $fillable = [
        'name',
        'email',
        'country_code',
        'phone_no',
        'security_question',
        'security_answer',
        'recovery_email',
        'recovery_country_code',
        'recovery_phone_no',
        'active'
        
    ];

    protected $allowedSorts = [
       
        'created_at',
        'updated_at'
    ];

    protected $hidden = ['deleted_at'];

    
    public function users()
    {
        return $this->belongsToMany(User::class,'accounts_users', 'account_id', 'user_id')->withPivot('role','permissions');
    }
    public function categories()
    {
        return $this->hasMany(Category::class,'account_id')->whereNull('parent_id');
    }
    public function contractTags()
    {
        return $this->hasMany(ContractTag::class,'account_id')->whereNull('project_id');
    }

    public function docTypes()
    {
        return $this->hasMany(DocType::class,'account_id')->whereNull('project_id');
    }

    public function contractSettings()
    {
        return $this->hasMany(ContractSetting::class,'account_id')->whereNull('project_id');
    }

    public function projectFolders()
    {
        return $this->hasMany(ProjectFolder::class,'account_id')->whereNull('project_id');
    }
    
   
    
}
