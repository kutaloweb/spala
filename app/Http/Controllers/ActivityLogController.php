<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Repositories\UserRepository;
use App\Repositories\ActivityLogRepository;
use Illuminate\Validation\ValidationException;

class ActivityLogController extends Controller
{
    /**
     * @var string
     */
    protected $module = 'activity_log';

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var ActivityLogRepository
     */
    protected $repo;

    /**
     * @var UserRepository
     */
    protected $user;

    /**
     * Instantiate a new controller instance
     *
     * @param Request $request
     * @param ActivityLogRepository $repo
     * @param UserRepository $user
     */
    public function __construct(Request $request, ActivityLogRepository $repo, UserRepository $user)
    {
        $this->request = $request;
        $this->repo = $repo;
        $this->user = $user;

        $this->middleware('permission:access-configuration');
    }

    /**
     * Get all activity logs
     *
     * @return JsonResponse
     */
    public function index()
    {
        $activity_logs = $this->repo->paginate($this->request->all());

        $users = $this->user->getAll();

        return $this->success(compact('users', 'activity_logs'));
    }

    /**
     * Delete activity log
     *
     * @param int $id
     *
     * @return JsonResponse
     * @throws ValidationException
     * @throws \Exception
     */
    public function destroy($id)
    {
        $activity_log = $this->repo->findOrFail($id);

        $this->repo->record([
            'module' => $this->module,
            'module_id' => $activity_log->id,
            'activity' => 'deleted'
        ]);

        $this->repo->delete($activity_log);

        return $this->success(['message' => trans('activity.deleted', ['activity' => trans('activity.activity')])]);
    }
}
