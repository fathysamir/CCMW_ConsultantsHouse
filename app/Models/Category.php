<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Casts\CustomDateTimeCast;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'categories';



    protected $fillable = [
        'code',
        'name',
        'account_id',
        'parent_id',
        'eps_order'

    ];

    protected $allowedSorts = [

        'created_at',
        'updated_at'
    ];

    protected $hidden = ['deleted_at'];

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }
    public function getRootCategory($stopAtParentId = null)
{
    $category = $this;

    while ($category->parent && $category->parent_id != $stopAtParentId) {
        $category = $category->parent;
    }

    return $category;
}
    // public function getRootCategory()
    // {
    //     return $this->parent ? $this->parent->getRootCategory() : $this;
    // }
    /**
     * Get the child accounts.
     */
    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }
    public function allChildren()
    {
        // Load the children for this category, and then each child's children, recursively
        return $this->children()->with('allChildren');
    }

    public function getAllParentIds(): array
    {
        $parentIds = [];
        $parent = $this->parent;

        while ($parent) {
            $parentIds[] = $parent->id;
            $parent = $parent->parent;
        }

        return $parentIds;
    }
    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id', 'id')->withTrashed();
    }
    public function projects()
    {
        return $this->hasMany(Project::class, 'category_id');
    }

}
