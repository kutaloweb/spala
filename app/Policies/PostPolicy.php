<?php

namespace App\Policies;

use App\User;
use App\Post;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Auth;

class PostPolicy
{
    use HandlesAuthorization;

    /**
     * Authorize the action before the intended policy method is called.
     *
     * @param $user
     * @param $ability
     *
     * @return bool
     */
    public function before($user, $ability)
    {
        return $user->can('access-post');
    }

    /**
     * Determine whether the user can view all the posts.
     *
     * @param User $user
     *
     * @return bool
     */
    public function view(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can create posts.
     *
     * @param User $user
     *
     * @return bool
     */
    public function create(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can view the post.
     *
     * @param User $user
     * @param Post $post
     *
     * @return bool
     */
    public function show(User $user, Post $post)
    {
        return $post->user_id === $user->id || Auth::user()->hasRole('admin');
    }

    /**
     * Determine whether the user can update the post.
     *
     * @param User $user
     * @param Post $post
     *
     * @return bool
     */
    public function update(User $user, Post $post)
    {
        return $post->user_id === $user->id || Auth::user()->hasRole('admin');
    }

    /**
     * Determine whether the user can delete the post.
     *
     * @param User $user
     * @param Post $post
     *
     * @return bool
     */
    public function delete(User $user, Post $post)
    {
        return $post->user_id === $user->id || Auth::user()->hasRole('admin');
    }
}
