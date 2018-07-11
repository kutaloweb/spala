<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Locale
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $locale
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read string $locale_with_name
 */
class Locale extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'locale'
    ];

    /**
     * Get the local and the local name.
     *
     * @return string
     */
    public function getLocaleWithNameAttribute()
    {
        return $this->name . ' (' . $this->locale . ')';
    }

    /**
     * Scope a query to only include locals with the given name.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $locale
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilterByLocale($query, $locale)
    {
        if (!$locale) {
            return $query;
        }

        return $query->where('locale', '=', $locale);
    }
}
