<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\RoleRequest;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;
use App\Repositories\RoleRepository;
use App\Repositories\ActivityLogRepository;

class RoleController extends Controller
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var RoleRepository
     */
    protected $repo;

    /**
     * @var ActivityLogRepository
     */
    protected $activity;

    /**
     * @var string
     */
    protected $module = 'role';

    /**
     * Instantiate a new controller instance
     *
     * @param Request $request
     * @param RoleRepository $repo
     * @param ActivityLogRepository $activity
     */
    public function __construct(Request $request, RoleRepository $repo, ActivityLogRepository $activity)
    {
        $this->request = $request;
        $this->repo = $repo;
        $this->activity = $activity;

        $this->middleware('permission:access-configuration');
    }

    /**
     * Get all roles
     *
     * @return LengthAwarePaginator
     */
    public function index()
    {
        return $this->repo->paginate($this->request->all());
    }

    /**
     * Store role
     *
     * @param RoleRequest $request
     *
     * @return JsonResponse
     */
    public function store(RoleRequest $request)
    {
        $role = Role::create(['name' => request('name')]);

        $this->activity->record([
            'module' => $this->module,
            'module_id' => $role->id,
            'activity' => 'added'
        ]);

        return $this->success(['message' => trans('role.added')]);
    }

    /**
     * @param int $id
     *
     * @return \App\Role
     * @throws ValidationException
     */
    public function show($id)
    {
        return $this->repo->findOrFail($id);
    }

    /**
     * Delete role
     *
     * @param int $id
     *
     * @return JsonResponse
     * @throws ValidationException
     * @throws \Exception
     */
    public function destroy($id)
    {
        $role = $this->repo->deletable($id);
        $this->activity->record([
            'module' => $this->module,
            'module_id' => $role->id,
            'activity' => 'deleted'
        ]);

        $this->repo->delete($role);

        return $this->success(['message' => trans('role.deleted')]);
    }
}
