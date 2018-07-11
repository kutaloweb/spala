<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * App\ActivityLog
 *
 * @property int $id
 * @property int $user_id
 * @property int|null $login_as_user_id
 * @property string|null $user_agent
 * @property string|null $module
 * @property string|null $module_id
 * @property string|null $sub_module
 * @property string|null $sub_module_id
 * @property string|null $activity
 * @property string $ip
 * @property string $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\User $user
 */
class ActivityLog extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'module',
        'sub_module',
        'user_agent',
        'module_id',
        'sub_module_id',
        'activity',
        'ip'
    ];

    /**
     * Get the user that owns the activity log.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
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
     * Scope a query to only include activities of user.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int|null $user_id
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilterByUserId($query, $user_id = null)
    {
        if (!$user_id) {
            return $query;
        }

        return $query->whereUserId($user_id);
    }

    /**
     * Scope a query to only include activities between dates.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $dates
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
