<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;
use App\Repositories\RoleRepository;
use App\Http\Requests\PermissionRequest;
use Spatie\Permission\Models\Permission;
use App\Repositories\PermissionRepository;
use App\Repositories\ActivityLogRepository;

class PermissionController extends Controller
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var PermissionRepository
     */
    protected $repo;

    /**
     * @var ActivityLogRepository
     */
    protected $activity;

    /**
     * @var RoleRepository
     */
    protected $role;

    /**
     * @var string
     */
    protected $module = 'permission';

    /**
     * Instantiate a new controller instance
     *
     * @param Request $request
     * @param PermissionRepository $repo
     * @param ActivityLogRepository $activity
     * @param RoleRepository $role
     */
    public function __construct(Request $request, PermissionRepository $repo, ActivityLogRepository $activity, RoleRepository $role)
    {
        $this->repo = $repo;
        $this->request = $request;
        $this->activity = $activity;
        $this->middleware('permission:access-configuration');
        $this->role = $role;
    }

    /**
     * Get all permissions
     *
     * @return LengthAwarePaginator
     */
    public function index()
    {
        return $this->repo->paginate($this->request->all());
    }

    /**
     * Fetch pre-requisites during permission assign
     * 
     * @return JsonResponse
     */
    public function preRequisite()
    {
        $roles = $this->role->getAll();
        $permissions = $this->repo->getAll();
        $assignedPermissions = DB::table('role_has_permissions')->get();

        $data = [];
        foreach ($permissions as $permission) {
            foreach ($roles as $role) {
                $data[$role->id][$permission->id] = $assignedPermissions
                    ->where('role_id', $role->id)
                    ->where('permission_id', $permission->id)
                    ->count() ? true : false;
            }
        }

        return $this->success(compact('roles', 'permissions', 'data'));
    }

    /**
     * Assign permission
     *
     * @return JsonResponse
     */
    public function assignPermission()
    {
        $input = request('data');
        $roles = $this->role->list();

        foreach ($input as $role_id => $data) {
            $role = Role::findByName($roles[$role_id]);
            $permissions = [];
            foreach ($data as $permission_id => $value) {
                if ($value) {
                    $permissions[] = $permission_id;
                }
            }
            if ($role->name === $this->role->findByName(config('system.default_role.admin'))->name) {
                $role->syncPermissions($this->repo->listByName());
            } else {
                $role->syncPermissions($this->repo->listByName($permissions));
            }
        }

        $this->activity->record([
            'module' => $this->module,
            'activity' => 'assigned'
        ]);

        return $this->success(['message' => trans('permission.assigned')]);
    }

    /**
     * Store permission
     *
     * @param PermissionRequest $request
     *
     * @return JsonResponse
     */
    public function store(PermissionRequest $request)
    {
        $permission = Permission::create(['name' => request('name')]);

        $this->activity->record([
            'module' => $this->module,
            'module_id' => $permission->id,
            'activity' => 'added'
        ]);

        return $this->success(['message' => trans('permission.added')]);
    }

    /**
     * Get permission details
     *
     * @param int $id
     *
     * @return \App\Permission
     * @throws ValidationException
     */
    public function show($id)
    {
        return $this->repo->findOrFail($id);
    }

    /**
     * Delete permission
     *
     * @param int $id
     *
     * @return JsonResponse
     * @throws ValidationException
     * @throws \Exception
     */
    public function destroy($id)
    {
        $permission = $this->repo->findOrFail($id);

        $this->activity->record([
            'module' => $this->module,
            'module_id' => $permission->id,
            'activity' => 'deleted'
        ]);

        $this->repo->delete($permission);

        return $this->success(['message' => trans('permission.deleted')]);
    }
}
