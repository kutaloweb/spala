<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * App\Profile
 *
 * @property int $id
 * @property int $user_id
 * @property int $is_draft
 * @property string|null $title
 * @property string|null $slug
 * @property string|null $body
 * @property string|null $cover
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read string|null $stripped_body
 * @property-read \App\User $user
 * @property-read \App\Category $category
 */
class Post extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'body'
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['stripped_body', 'totally_stripped_body'];

    /**
     * Get the user that owns the post.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the category.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get created at in a human readable format.
     *
     * @return string
     */
    public function getCreatedAtAttribute()
    {
        return Carbon::parse($this->attributes['created_at'])->diffForHumans();
    }

    /**
     * Get updated at in a human readable format.
     *
     * @return string
     */
    public function getUpdatedAtAttribute()
    {
        return Carbon::parse($this->attributes['updated_at'])->diffForHumans();
    }

    /**
     * Get body with the stripped HTML tags allowing only <p> tag.
     *
     * @return string
     */
    public function getStrippedBodyAttribute()
    {
        $string = $this->attributes['body'];
        $doubleSpace = strip_tags(str_replace('<', ' <', $string), '<p>');
        $singleSpace = str_replace('  ', ' ', $doubleSpace);

        return trim($singleSpace);
    }

    /**
     * Get body with the stripped HTML tags.
     *
     * @return string
     */
    public function getTotallyStrippedBodyAttribute()
    {
        $string = $this->attributes['body'];
        $doubleSpace = strip_tags(str_replace('<', ' <', $string));
        $singleSpace = str_replace('  ', ' ', $doubleSpace);
        $string = trim($singleSpace);
        if (mb_strlen($string, "utf-8") > 150) {
            $string = mb_substr($string, 0, 150, "utf-8") . '...';
        }
        return $string;
    }

    /**
     * Get cover image.
     *
     * @return string
     */
    public function getCoverAttribute()
    {
        return $this->attributes['cover'] ?: config('config.default_cover');
    }

    /**
     * Sets the title and the readable slug.
     *
     * @param string $value
     */
    public function setTitleAttribute($value)
    {
        $this->attributes['title'] = $value;
    }

    /**
     * Sets the unique slug.
     *
     * @param $value
     */
    public function setUniqueSlug($value)
    {
        $slug = str_slug($value);
        if (static::whereSlug($slug)->exists()) {
            $slug = str_slug($value . '-' . str_random(5));
            $this->attributes['slug'] = $slug;
            return;
        }

        $this->attributes['slug'] = $slug;
    }

    /**
     * Scope a query to only include drafts.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $is_draft
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilterByIsDraft($query, $is_draft)
    {
        return $query->where('is_draft', '=', $is_draft);
    }

    /**
     * Scope a query to only include post with the given slug.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $slug
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilterBySlug($query, $slug = null)
    {
        if (!$slug) {
            return $query;
        }

        return $query->where('slug', '=', $slug);
    }

    /**
     * Scope a query to only include post with the given category and slug.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param Category $category
     * @param string $slug
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilterByCategoryAndSlug($query, $category, $slug)
    {
        return $query->where('slug', '=', $slug)->where('category_id', '=', $category->id);
    }

    /**
     * Scope a query to only include post with the given id.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int|null $id
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilterById($query, $id = null)
    {
        if (!$id) {
            return $query;
        }

        return $query->where('id', '=', $id);
    }

    /**
     * Scope a query to only include user posts.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int|null $user_id
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilterByUserId($query, $user_id = null)
    {
        if (!$user_id || Auth::user()->hasRole('admin')) {
            return $query;
        }

        return $query->where('user_id', '=', $user_id);
    }

    /**
     * Scope a query to only include posts with the given title.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $search_query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilterBySearchQuery($query, $search_query = null)
    {
        if (!$search_query) {
            return $query;
        }

        $search = "%" . str_replace(" ", "%", $search_query) . "%";

        return $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")->orWhere('body', 'like', "%{$search}%");
        });
    }

    /**
     * Scope a query to only include category posts.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int|null $category_id
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilterByCategoryId($query, $category_id = null)
    {
        if (!$category_id) {
            return $query;
        }

        return $query->whereCategoryId($category_id);
    }

    /**
     * Scope a query to only include posts between dates.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string status
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCreatedAtDateBetween($query, $dates)
    {
        if ((!$dates['start_date'] || !$dates['end_date']) && $dates['start_date'] <= $dates['end_date']) {
            return $query;
        }

        return $query->where('created_at', '>=', getStartOfDate($dates['start_date']))->where('created_at', '<=', getEndOfDate($dates['end_date']));
    }
}
