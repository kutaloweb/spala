<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Role
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read string $detail
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Post[] $posts
 */
class Category extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'slug'
    ];

    /**
     * Get the category posts.
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    /**
     * Scope a query to only include categories with the given name.
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
