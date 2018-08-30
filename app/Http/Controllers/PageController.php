<?php

namespace App\Http\Controllers;

use App\Repositories\CategoryRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use App\Http\Requests\PageRequest;
use App\Repositories\PageRepository;
use App\Repositories\ActivityLogRepository;

class PageController extends Controller
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var PageRepository
     */
    protected $repo;

    /**
     * @var ActivityLogRepository
     */
    protected $activity;

    /**
     * @var CategoryRepository
     */
    protected $category;

    /**
     * @var string
     */
    protected $module = 'page';

    /**
     * Instantiate a new controller instance.
     *
     * @param Request $request
     * @param PageRepository $repo
     * @param ActivityLogRepository $activity
     * @param CategoryRepository $category
     */
    public function __construct(Request $request, PageRepository $repo, ActivityLogRepository $activity, CategoryRepository $category)
    {
        $this->request = $request;
        $this->repo = $repo;
        $this->activity = $activity;
        $this->category = $category;
        $this->middleware('permission:access-page')->except(['getPublicPages', 'getPublicPage']);
    }

    /**
     * Display all public pages
     *
     * @return JsonResponse
     */
    public function getPublicPages()
    {
        $pages = $this->repo->getPublished();

        return $this->success(compact('pages'));
    }

    /**
     * Display a public page
     *
     * @param string $slug
     *
     * @return JsonResponse
     */
    public function getPublicPage($slug)
    {
        $categories = $this->category->getAll();

        $page = $this->repo->getBySlugForGuests($slug);

        return $this->success(compact('categories', 'page'));
    }

    /**
     * Store page
     *
     * @param PageRequest $request
     *
     * @return JsonResponse
     * @throws ValidationException
     */
    public function store(PageRequest $request)
    {
        $page = $this->repo->create($this->request->all());

        $this->activity->record([
            'module' => $this->module,
            'module_id' => $page->id,
            'activity' => 'published'
        ]);

        return $this->success(['page' => trans('page.page_processed', ['action' => trans('page.published')])]);
    }

    /**
     * Upload image in Summernote editor
     *
     * @param PageRequest $request
     *
     * @return JsonResponse
     */
    public function uploadImage(PageRequest $request)
    {
        $upload_path = config('system.upload_path.images');
        $extension = request()->file('file')->getClientOriginalExtension();
        $filename = uniqid();
        request()->file('file')->move($upload_path, $filename . "." . $extension);
        $image_url = '/' . $upload_path . '/' . $filename . '.' . $extension;

        return $this->success(compact('image_url'));
    }

    /**
     * Fetch statistics
     *
     * @return JsonResponse
     */
    public function statistics()
    {
        $published = $this->repo->getPublished()->count();

        return $this->success(compact('published'));
    }

    /**
     * Get all published pages
     *
     * @return JsonResponse
     */
    public function getPublishedList()
    {
        $pages = $this->repo->getPublishedList($this->request->all());

        return $this->success(compact('pages'));
    }

    /**
     * Get page details
     *
     * @param string $slug
     *
     * @return JsonResponse
     * @throws ValidationException
     */
    public function show($slug)
    {
        $page = $this->repo->getBySlug($slug);

        return $this->success(compact('page'));
    }

    /**
     * Delete page
     *
     * @param string $slug
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function destroy($slug)
    {
        $page = $this->repo->getBySlug($slug);

        $this->activity->record([
            'module' => 'page',
            'sub_module' => 'page',
            'module_id' => $page->id,
            'activity' => 'deleted'
        ]);

        $page->delete();

        return $this->success(['page' => trans('page.deleted', ['type' => trans('page.page')])]);
    }
}
