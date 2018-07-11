<?php

namespace App\Notifications;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class PasswordReset extends Notification
{
    use Queueable;

    /**
     * @var User
     */
    protected $user;

    /**
     * @var string
     */
    protected $token;

    /**
     * Create a new notification instance.
     *
     * @param User $user
     * @param string $token
     */
    public function __construct($user, $token)
    {
        $this->user = $user;
        $this->token = $token;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     *
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Send password reset mail after request
     *
     * @param mixed $notifiable
     *
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $url = url('/password/reset/' . $this->token);

        return (new MailMessage)
            ->subject(trans('notification.password_reset_subject') . ' | ' . config('config.company_name'))
            ->greeting(trans('notification.hello') . $this->user->profile->first_name)
            ->line(trans('notification.password_reset_request'))
            ->line(trans('notification.password_reset_click'))
            ->action(trans('notification.password_reset_action'), $url)
            ->line(trans('notification.password_reset_ignore'))
            ->line(trans('notification.thankyou'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     *
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
