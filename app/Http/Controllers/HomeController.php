<?php

namespace App\Http\Controllers;

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
     * Instantiate a new controller instance
     *
     * @param UserRepository $user
     * @param ActivityLogRepository $activity
     */
    public function __construct(UserRepository $user, ActivityLogRepository $activity)
    {
        $this->user = $user;
        $this->activity = $activity;
    }

    /**
     * Get dashboard statistics.
     */
    public function dashboard()
    {
        if (Auth::user()->hasRole(config('system.default_role.admin'))) {
            $users = $this->user->count();
            $today_registered_users = $this->user->countDateBetween(date('Y-m-d'), date('Y-m-d'));
            $weekly_registered_users = $this->user->countDateBetween(date('Y-m-d', strtotime("-7 days")), date('Y-m-d'));
            $monthly_registered_users = $this->user->countDateBetween(date('Y-m-d', strtotime("-1 months")), date('Y-m-d'));
        }

        $activity_logs = $this->activity->getQuery();

        if (!Auth::user()->hasRole(config('system.default_role.admin'))) {
            $activity_logs->filterByUserId(Auth::user()->id);
        }

        $activity_logs = $activity_logs->orderBy('created_at', 'desc')->take(10)->get();

        return $this->success(compact('users', 'today_registered_users', 'weekly_registered_users', 'monthly_registered_users', 'activity_logs'));
    }
}
