<?php

namespace App\Http\Controllers;

use App\Repositories\PostRepository;
use Illuminate\Support\Facades\Auth;
use App\Repositories\UserRepository;
use App\Repositories\ActivityLogRepository;

class HomeController extends Controller
{
    /**
     * @var UserRepository
     */
    protected $user;

    /**
     * @var ActivityLogRepository
     */
    protected $activity;

    /**
     * @var PostRepository
     */
    protected $post;

    /**
     * Instantiate a new controller instance
     *
     * @param UserRepository $user
     * @param ActivityLogRepository $activity
     * @param PostRepository $post
     */
    public function __construct(UserRepository $user, ActivityLogRepository $activity, PostRepository $post)
    {
        $this->user = $user;
        $this->activity = $activity;
        $this->post = $post;
    }

    /**
     * Get dashboard statistics.
     */
    public function dashboard()
    {
        if (Auth::user()->hasRole(config('system.default_role.admin'))) {
            $all_registered_users = $this->user->count();
            $today_registered_users = $this->user->countDateBetween(date('Y-m-d'), date('Y-m-d'));
            $weekly_registered_users = $this->user->countDateBetween(date('Y-m-d', strtotime("-7 days")), date('Y-m-d'));
            $monthly_registered_users = $this->user->countDateBetween(date('Y-m-d', strtotime("-1 months")), date('Y-m-d'));

            $all_published_posts = $this->post->count();
            $today_published_posts = $this->post->countDateBetween(date('Y-m-d'), date('Y-m-d'));
            $weekly_published_posts = $this->post->countDateBetween(date('Y-m-d', strtotime("-7 days")), date('Y-m-d'));
            $monthly_published_posts = $this->post->countDateBetween(date('Y-m-d', strtotime("-1 months")), date('Y-m-d'));

            $records = 10;
            $activity_logs = $this->activity->getQuery()->orderBy('created_at', 'desc')->take($records)->get();
            $posts = $this->post->getQuery()->orderBy('created_at', 'desc')->take($records)->get();
        }

        return $this->success(compact(
                'all_registered_users',
                'today_registered_users',
                'weekly_registered_users',
                'monthly_registered_users',
                'all_published_posts',
                'today_published_posts',
                'weekly_published_posts',
                'monthly_published_posts',
                'activity_logs',
                'posts'
            )
        );
    }
}
