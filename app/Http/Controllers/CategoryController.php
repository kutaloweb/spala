<?php

namespace App\Http\Controllers;

use App\Category;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\CategoryRequest;
use Illuminate\Validation\ValidationException;
use App\Repositories\CategoryRepository;
use App\Repositories\ActivityLogRepository;

class CategoryController extends Controller
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var CategoryRepository
     */
    protected $repo;

    /**
     * @var ActivityLogRepository
     */
    protected $activity;

    /**
     * @var string
     */
    protected $module = 'category';

    /**
     * Instantiate a new controller instance
     *
     * @param Request $request
     * @param CategoryRepository $repo
     * @param ActivityLogRepository $activity
     */
    public function __construct(Request $request, CategoryRepository $repo, ActivityLogRepository $activity)
    {
        $this->request = $request;
        $this->repo = $repo;
        $this->activity = $activity;

        $this->middleware('permission:access-category');
    }

    /**
     * Get all categories
     *
     * @return LengthAwarePaginator
     */
    public function index()
    {
        return $this->repo->paginate($this->request->all());
    }

    /**
     * Store category
     *
     * @param CategoryRequest $request
     *
     * @return JsonResponse
     */
    public function store(CategoryRequest $request)
    {
        if (Category::where('slug', str_slug(request('name')))->exists()) {
            return $this->error(['message' => trans('category.exists')]);
        }
        $category = Category::create([
            'name' => request('name'),
            'slug' => str_slug(request('name')),
        ]);

        $this->activity->record([
            'module' => $this->module,
            'module_id' => $category->id,
            'activity' => 'added'
        ]);

        return $this->success(['message' => trans('category.added')]);
    }

    /**
     * @param int $id
     *
     * @return \App\Category
     * @throws ValidationException
     */
    public function show($id)
    {
        return $this->repo->findOrFail($id);
    }

    /**
     * Delete category
     *
     * @param int $id
     *
     * @return JsonResponse
     * @throws ValidationException
     * @throws \Exception
     */
    public function destroy($id)
    {
        $category = $this->repo->deletable($id);
        $this->activity->record([
            'module' => $this->module,
            'module_id' => $category->id,
            'activity' => 'deleted'
        ]);

        $this->repo->delete($category);

        return $this->success(['message' => trans('category.deleted')]);
    }
}
