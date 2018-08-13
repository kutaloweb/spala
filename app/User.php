<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * App\User
 *
 * @property int $id
 * @property string $email
 * @property string|null $password
 * @property string|null $activation_token
 * @property string|null $status
 * @property string|null $remember_token
 * @property string $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read string $name
 * @property-read string $name_with_email
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Permission\Models\Permission[] $permissions
 * @property-read \App\Profile $profile
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Permission\Models\Role[] $roles
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Post[] $posts
 */
class User extends Authenticatable implements JWTSubject
{
    use Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'activation_token'
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['name'];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * Get the profile associated with the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    /**
     * Get the user posts.
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    /**
     * Get user full name
     *
     * @return string
     */
    public function getNameAttribute()
    {
        return $this->profile->first_name . ' ' . $this->profile->last_name;
    }

    /**
     * Get user full name and email
     *
     * @return string
     */
    public function getNameWithEmailAttribute()
    {
        return $this->profile->first_name . ' ' . $this->profile->last_name . ' (' . $this->email . ')';
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
     * Scope a query to only include users with the given email.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $email
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilterByEmail($query, $email = null)
    {
        if (!$email) {
            return $query;
        }

        return $query->where('email', 'like', '%' . $email . '%');
    }

    /**
     * Scope a query to only include users with the given first name.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $first_name
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilterByFirstName($query, $first_name = null)
    {
        if (!$first_name) {
            return $query;
        }

        return $query->whereHas('profile', function ($r) use ($first_name) {
            $r->where('first_name', 'like', '%' . $first_name . '%');
        });
    }

    /**
     * Scope a query to only include users with the given last name.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $last_name
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilterByLastName($query, $last_name = null)
    {
        if (!$last_name) {
            return $query;
        }

        return $query->whereHas('profile', function ($r) use ($last_name) {
            $r->where('last_name', 'like', '%' . $last_name . '%');
        });
    }

    /**
     * Scope a query to only include users with the given role id.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string role_id
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilterByRoleId($query, $role_id = null)
    {
        if (!$role_id) {
            return $query;
        }

        return $query->whereHas('roles', function ($r) use ($role_id) {
            $r->where('role_id', '=', $role_id);
        });
    }

    /**
     * Scope a query to only include users with the given status.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string status
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilterByStatus($query, $status = null)
    {
        if (!$status) {
            return $query;
        }

        return $query->where('status', '=', $status);
    }

    /**
     * Scope a query to only include users between dates.
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
