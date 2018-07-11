<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Configuration
 *
 * @property int $id
 * @property string|null $name
 * @property int|null $numeric_value
 * @property string|null $text_value
 * @property int $is_private
 * @property-read string $value
 */
class Configuration extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'numeric_value',
        'text_value',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'config';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Get the numeric value.
     *
     * @return string
     */
    public function getValueAttribute()
    {
        return ($this->numeric_value != null) ? $this->numeric_value : $this->text_value;
    }

    /**
     * Scope a query to only include configs with the given.
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

        return $query->where('name', '=', $name);
    }
}
