<?php

namespace App\Repositories;

use App\Permission;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

class PermissionRepository
{
    /**
     * @var Permission
     */
    protected $permission;

    /**
     * Instantiate a new Repository instance.
     *
     * @param Permission $permission
     */
    public function __construct(Permission $permission)
    {
        $this->permission = $permission;
    }

    /**
     * Get all permissions
     *
     * @return array
     */
    public function getAll()
    {
        return $this->permission->all();
    }

    /**
     * List permission names
     *
     * @param array $ids
     *
     * @return array
     */
    public function listByName($ids = [])
    {
        if (count($ids)) {
            return $this->permission->whereIn('id', $ids)->get()->pluck('name')->all();
        } else {
            return $this->permission->all()->pluck('name')->all();
        }
    }

    /**
     * List all permission names
     *
     * @return array
     */
    public function listName()
    {
        return $this->permission->all()->pluck('name')->all();
    }

    /**
     * Find permission with given id.
     *
     * @param int $id
     *
     * @return Permission
     * @throws ValidationException
     */
    public function findOrFail($id)
    {
        $permission = $this->permission->find($id);

        if (!$permission) {
            throw ValidationException::withMessages(['message' => trans('permission.could_not_find')]);
        }

        return $permission;
    }

    /**
     * Paginate all permissions using given params.
     *
     * @param array $params
     *
     * @return LengthAwarePaginator
     */
    public function paginate($params)
    {
        $sort_by = isset($params['sort_by']) ? $params['sort_by'] : 'created_at';
        $order = isset($params['order']) ? $params['order'] : 'desc';
        $page_length = isset($params['page_length']) ? $params['page_length'] : config('config.page_length');

        return $this->permission->orderBy($sort_by, $order)->paginate($page_length);
    }

    /**
     * Record a new permission.
     *
     * @param array $params
     *
     * @return Permission
     */
    public function record($params)
    {
        return $this->permission->forceCreate($this->formatParams($params));
    }

    /**
     * Prepare given params for inserting into database.
     *
     * @param array $params
     *
     * @return array
     */
    private function formatParams($params)
    {
        $formatted = [
            'user_id' => isset($params['userId']) ? $params['userId'] : \Auth::user()->id,
            'module' => isset($params['module']) ? $params['module'] : null,
            'module_id' => isset($params['module_id']) ? $params['module_id'] : null,
            'sub_module' => isset($params['sub_module']) ? $params['sub_module'] : null,
            'sub_module_id' => isset($params['sub_moduleId']) ? $params['sub_moduleId'] : null,
            'activity' => isset($params['activity']) ? $params['activity'] : null,
            'ip' => getClientIp(),
            'user_agent' => \Request::header('User-Agent')
        ];

        return $formatted;
    }

    /**
     * Delete permission.
     *
     * @param Permission $permission
     *
     * @return bool|null
     * @throws \Exception
     */
    public function delete(Permission $permission)
    {
        return $permission->delete();
    }

    /**
     * Delete multiple permissions.
     *
     * @param array $ids
     *
     * @return bool|null
     * @throws \Exception
     */
    public function deleteMultiple($ids)
    {
        return $this->permission->whereIn('id', $ids)->delete();
    }
}
