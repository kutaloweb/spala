<?php

namespace App\Http\Controllers;

use App\Post;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use App\Repositories\UserRepository;
use App\Http\Requests\PostRequest;
use App\Repositories\PostRepository;
use App\Repositories\ActivityLogRepository;

class PostController extends Controller
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var PostRepository
     */
    protected $repo;

    /**
     * @var ActivityLogRepository
     */
    protected $activity;

    /**
     * @var UserRepository
     */
    protected $user;

    /**
     * @var string
     */
    protected $module = 'post';

    /**
     * Instantiate a new controller instance.
     *
     * @param Request $request
     * @param PostRepository $repo
     * @param ActivityLogRepository $activity
     * @param UserRepository $user
     */
    public function __construct(Request $request, PostRepository $repo, ActivityLogRepository $activity, UserRepository $user)
    {
        $this->request = $request;
        $this->repo = $repo;
        $this->activity = $activity;
        $this->user = $user;
        $this->middleware('permission:access-post');
    }

    /**
     * Store post
     *
     * @param PostRequest $request
     *
     * @return JsonResponse
     * @throws ValidationException
     * @throws AuthorizationException
     */
    public function store(PostRequest $request)
    {
        $this->authorize('create', Post::class);

        $post = $this->repo->create($this->request->all());

        $this->activity->record([
            'module' => $this->module,
            'module_id' => $post->id,
            'activity' => $request->is_draft ? 'drafted' : 'published'
        ]);

        return $this->success(['post' => trans('post.post_processed', ['action' => request('is_draft') ? trans('post.drafted') : trans('post.published')])]);
    }

    /**
     * Upload image in Summernote editor
     *
     * @param PostRequest $request
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function uploadImage(PostRequest $request)
    {
        $this->authorize('create', Post::class);

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
        $draft = $this->repo->getDraft()->count();

        return $this->success(compact('published', 'draft'));
    }

    /**
     * Get all draft posts
     *
     * @return Post[]|Collection
     */
    public function getDraftList()
    {
        return $this->repo->getDraftList($this->request->all());
    }

    /**
     * Get all published posts
     *
     * @return Post[]|Collection
     */
    public function getPublishedList()
    {
        return $this->repo->getPublishedList($this->request->all());
    }

    /**
     * Get post details
     *
     * @param string $slug
     *
     * @return JsonResponse
     * @throws ValidationException
     * @throws AuthorizationException
     */
    public function show($slug)
    {
        $post = $this->repo->getBySlug($slug);

        $this->authorize('show', $post);

        return $this->success(compact('post'));
    }

    /**
     * Delete post
     *
     * @param string $slug
     *
     * @return JsonResponse
     * @throws \Exception
     * @throws AuthorizationException
     */
    public function destroy($slug)
    {
        $post = $this->repo->getBySlug($slug);

        $this->authorize('delete', $post);

        $this->activity->record([
            'module' => 'post',
            'sub_module' => $post->is_draft ? 'draft' : 'post',
            'module_id' => $post->id,
            'activity' => 'deleted'
        ]);
        $post->delete();

        return $this->success(['post' => trans('post.deleted', ['type' => trans('post.post')])]);
    }
}
