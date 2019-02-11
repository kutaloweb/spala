<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;;

/**
 * App\Profile
 *
 * @property int $id
 * @property string|null $title
 * @property string|null $slug
 * @property string|null $body
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read string|null $stripped_body
 */
class Page extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'body',
        'slug'
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['totally_stripped_body'];

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
     * Sets the title and the readable slug.
     *
     * @param string $value
     */
    public function setTitleAttribute($value)
    {
        $this->attributes['title'] = $value;
    }

    /**
     * Scope a query to only include page with the given slug.
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
     * Scope a query to only include page with the given id.
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
}
