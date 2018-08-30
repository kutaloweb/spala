<?php

namespace App\Http\Controllers;

use App\Post;
use App\Repositories\CategoryRepository;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use App\Repositories\UserRepository;
use App\Http\Requests\PostRequest;
use App\Repositories\PostRepository;
use App\Repositories\ActivityLogRepository;
use Illuminate\Support\Facades\File;

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
     * @var CategoryRepository
     */
    protected $category;

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
     * @param CategoryRepository $category
     */
    public function __construct(Request $request, PostRepository $repo, ActivityLogRepository $activity, UserRepository $user, CategoryRepository $category)
    {
        $this->request = $request;
        $this->repo = $repo;
        $this->activity = $activity;
        $this->user = $user;
        $this->category = $category;
        $this->middleware('permission:access-post')->except(['getPublicPosts', 'getPublicPost']);
    }

    /**
     * Display all public posts
     *
     * @return JsonResponse
     */
    public function getPublicPosts()
    {
        $categories = $this->category->getAll();

        $posts = $this->repo->getPosts($this->request->all());

        return $this->success(compact('categories', 'posts'));
    }

    /**
     * Display a public post
     *
     * @param string $category
     * @param string $slug
     *
     * @return JsonResponse
     */
    public function getPublicPost($category, $slug)
    {
        $categories = $this->category->getAll();

        $post = $this->repo->getByCategoryAndSlug($category, $slug);

        return $this->success(compact('categories', 'post'));
    }

    /**
     * Get pre-requisites for post module
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function preRequisite()
    {
        $this->authorize('create', Post::class);

        return generateSelect($this->category->list());
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
     * @return JsonResponse
     */
    public function getDraftList()
    {
        $categories = $this->category->getAll();

        $posts = $this->repo->getDraftList($this->request->all());

        return $this->success(compact('categories', 'posts'));
    }

    /**
     * Get all published posts
     *
     * @return JsonResponse
     */
    public function getPublishedList()
    {
        $categories = $this->category->getAll();

        $posts = $this->repo->getPublishedList($this->request->all());

        return $this->success(compact('categories', 'posts'));
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
     * Update post cover image
     *
     * @param int $id
     *
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws ValidationException
     */
    public function uploadCover($id)
    {
        $post = $this->repo->findOrFail($id);

        $this->authorize('update', $post);

        $image_path = config('system.upload_path.images') . '/';
        $image = $post->cover;

        if ($image && File::exists($image) && $image != config('config.default_cover')) {
            File::delete($image);
        }

        $extension = request()->file('image')->getClientOriginalExtension();
        $filename = uniqid();
        request()->file('image')->move($image_path, $filename . "." . $extension);
        $img = \Image::make($image_path . $filename . "." . $extension);
        $img->resize(500, null, function ($constraint) {
            $constraint->aspectRatio();
        });
        $img->crop(500, 250);
        $img->save($image_path . $filename . "." . $extension);
        $post->cover = $image_path . $filename . "." . $extension;
        $post->save();

        $this->activity->record([
            'module' => $this->module,
            'module_id' => $post->id,
            'sub_module' => 'cover',
            'activity' => 'uploaded'
        ]);

        return $this->success(['message' => trans('post.cover_uploaded'), 'image' => $image_path . $filename . "." . $extension]);
    }

    /**
     * Remove post cover image
     *
     * @param int $id
     *
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws ValidationException
     */
    public function removeCover($id)
    {
        $post = $this->repo->findOrFail($id);

        $this->authorize('update', $post);

        $image = $post->cover;

        if ($image && File::exists($image) && $image != config('config.default_cover')) {
            File::delete($image);
        }

        $post->cover = null;
        $post->save();

        $this->activity->record([
            'module' => $this->module,
            'module_id' => $post->id,
            'sub_module' => 'cover',
            'activity' => 'removed'
        ]);

        return $this->success(['message' => trans('post.cover_removed')]);
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

        $image = $post->cover;

        if ($image && File::exists($image) && $image != config('config.default_cover')) {
            File::delete($image);
        }

        $post->delete();

        return $this->success(['post' => trans('post.deleted', ['type' => trans('post.post')])]);
    }
}
