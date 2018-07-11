<?php

namespace App\Listeners;

use App\Events\UserLogin;
use App\Profile;

class UserLoginListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Check profile associated with user
     *
     * @param UserLogin $event
     */
    public function handle(UserLogin $event)
    {
        $user = $event->user;

        $profile = $user->profile;
        if (!isset($profile) && $profile === '' && $profile === null) {
            $profile = new Profile;
            $profile->user()->associate($user);
            $profile->save();
        }
    }
}
