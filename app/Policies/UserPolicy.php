<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     *
     * @return bool
     */
    public function view(User $user)
    {
        return $user->can('list-user');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     *
     * @return bool
     */
    public function create(User $user)
    {
        return $user->can('create-user');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param User $model
     *
     * @return bool
     */
    public function update(User $user, User $model)
    {
        return $user->can('edit-user');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param User $model
     *
     * @return bool
     */
    public function delete(User $user, User $model)
    {
        if ($model->hasRole(config('system.default_role.admin'))) {
            return false;
        }

        return $user->can('delete-user');
    }

    /**
     * Determine whether the user can reset password the model.
     *
     * @param User $user
     * @param User $model
     *
     * @return bool
     */
    public function forceResetUserPassword(User $user, User $model)
    {
        return $user->can('force-reset-user-password') && $user->id != $model->id;
    }

    /**
     * Determine whether the user can perform avatar related action to the model.
     *
     * @param User $user
     * @param User $model
     *
     * @return bool
     */
    public function avatar(User $user, User $model)
    {
        return ($user->id === $model->id) || ($user->id != $model->id && $user->can('edit-user'));
    }
}
