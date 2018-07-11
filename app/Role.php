<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Role
 *
 * @property int $id
 * @property string $name
 * @property string $guard_name
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read string $detail
 */
class Role extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name'
    ];

    /**
     * Get the role name.
     *
     * @return string
     */
    public function getDetailAttribute()
    {
        return ucfirst($this->name);
    }

    /**
     * Scope a query to only include roles with the given name.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $name
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilterByName($query, $name = null)
    {
        if (!$name) {
            return $query;
        }

        return $query->where('name', 'like', '%' . $name . '%');
    }
}
