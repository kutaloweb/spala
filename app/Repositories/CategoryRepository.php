<?php

namespace App\Repositories;

use App\Category;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;

class CategoryRepository
{
    /**
     * @var Category
     */
    protected $category;

    /**
     * Instantiate a new Repository instance.
     *
     * @param Category $category
     */
    public function __construct(Category $category)
    {
        $this->category = $category;
    }

    /**
     * Get all categories
     *
     * @return array
     */
    public function getAll()
    {
        return $this->category->withCount('posts')->get();
    }

    /**
     * List all category names
     *
     * @return array
     */
    public function list()
    {
        return $this->category->all()->pluck('name', 'id')->all();
    }

    /**
     * Get category by name
     *
     * @param string|null $name
     *
     * @return Category
     */
    public function findByName($name = null)
    {
        return $this->category->filterByName($name)->first();
    }

    /**
     * List all categories by name where given name is not included
     *
     * @param array|null $names
     *
     * @return array
     */
    public function listExceptName($names = [])
    {
        return $this->category->whereNotIn('name', $names)->get()->pluck('name', 'id')->all();
    }

    /**
     * List all categories by ids
     *
     * @param array $ids
     *
     * @return array
     */
    public function listNameById($ids = [])
    {
        $ids = is_array($ids) ? $ids : ($ids) ? [$ids] : [];

        return $this->category->whereIn('id', $ids)->get()->pluck('name')->all();
    }

    /**
     * List all category names only
     *
     * @return array
     */
    public function listName()
    {
        return $this->category->all()->pluck('name')->all();
    }

    /**
     * Find activity log with given id or throw an error.
     *
     * @param int $id
     *
     * @return Category
     * @throws ValidationException
     */
    public function findOrFail($id)
    {
        $category = $this->category->find($id);

        if (!$category) {
            throw ValidationException::withMessages(['message' => trans('category.could_not_find')]);
        }

        return $category;
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

        return $this->category->orderBy($sort_by, $order)->paginate($page_length);
    }

    /**
     * Find category and check if it can be deleted or not.
     *
     * @param int $id
     *
     * @return Category
     * @throws ValidationException
     */
    public function deletable($id)
    {
        $category = $this->findOrFail($id);

        if (in_array($category->name, config('system.default_category'))) {
            throw ValidationException::withMessages(['message' => trans('category.default_cannot_be_deleted')]);
        }

        return $category;
    }

    /**
     * Delete activity log.
     *
     * @param Category $category
     *
     * @return bool|null
     * @throws \Exception
     */
    public function delete(Category $category)
    {
        try {
            return $category->delete();
        } catch (QueryException $e) {
            if ($e->errorInfo[1] === 1451) {
                throw ValidationException::withMessages(['message' => trans('category.integrity_constraint_violation')]);
            } else {
                throw $e;
            }
        }
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
        return $this->category->whereIn('id', $ids)->delete();
    }
}
