<?php

namespace App\Repositories;

use App\Role;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

class RoleRepository
{
    /**
     * @var Role
     */
    protected $role;

    /**
     * Instantiate a new Repository instance.
     *
     * @param Role $role
     */
    public function __construct(Role $role)
    {
        $this->role = $role;
    }

    /**
     * Get all roles
     *
     * @return array
     */
    public function getAll()
    {
        return $this->role->all();
    }

    /**
     * List all role names
     *
     * @return array
     */
    public function list()
    {
        return $this->role->all()->pluck('name', 'id')->all();
    }

    /**
     * Get role by name
     *
     * @param string|null $name
     *
     * @return Role
     */
    public function findByName($name = null)
    {
        return $this->role->filterByName($name)->first();
    }

    /**
     * List all roles by name where given name is not included
     *
     * @param array|null $names
     *
     * @return array
     */
    public function listExceptName($names = [])
    {
        return $this->role->whereNotIn('name', $names)->get()->pluck('name', 'id')->all();
    }

    /**
     * List all roles by ids
     *
     * @param array $ids
     *
     * @return array
     */
    public function listNameById($ids = [])
    {
        $ids = is_array($ids) ? $ids : ($ids) ? [$ids] : [];

        return $this->role->whereIn('id', $ids)->get()->pluck('name')->all();
    }

    /**
     * List all role names only
     *
     * @return array
     */
    public function listName()
    {
        return $this->role->all()->pluck('name')->all();
    }

    /**
     * Find activity log with given id or throw an error.
     *
     * @param int $id
     *
     * @return Role
     * @throws ValidationException
     */
    public function findOrFail($id)
    {
        $role = $this->role->find($id);

        if (!$role) {
            throw ValidationException::withMessages(['message' => trans('role.could_not_find')]);
        }

        return $role;
    }

    /**
     * Paginate all activity logs using given params.
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

        return $this->role->orderBy($sort_by, $order)->paginate($page_length);
    }

    /**
     * Find role and check if it can be deleted or not.
     *
     * @param int $id
     *
     * @return Role
     * @throws ValidationException
     */
    public function deletable($id)
    {
        $role = $this->findOrFail($id);

        if (in_array($role->name, config('system.default_role'))) {
            throw ValidationException::withMessages(['message' => trans('role.default_cannot_be_deleted')]);
        }

        return $role;
    }

    /**
     * Delete activity log.
     *
     * @param Role $role
     *
     * @return bool|null
     * @throws \Exception
     */
    public function delete(Role $role)
    {
        return $role->delete();
    }

    /**
     * Delete multiple activity logs.
     *
     * @param array $ids
     *
     * @return bool|null
     * @throws \Exception
     */
    public function deleteMultiple($ids = array())
    {
        return $this->role->whereIn('id', $ids)->delete();
    }
}
