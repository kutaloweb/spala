<?php

namespace App\Repositories;

use App\Post;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class PostRepository
{
    /**
     * @var Post
     */
    protected $post;

    /**
     * Instantiate a new controller instance.
     *
     * @param Post $post
     */
    public function __construct(Post $post)
    {
        $this->post = $post;
    }

    /**
     * Get valid post.
     *
     * @param string $slug
     *
     * @return Post
     * @throws ValidationException
     */
    public function getBySlug($slug)
    {
        $post = $this->post->with('user', 'user.profile')
            ->filterBySlug($slug)
            ->filterByUserId(\Auth::user()->id)
            ->first();

        if (!$post) {
            throw ValidationException::withMessages(['message' => trans('general.invalid_link')]);
        }

        return $post;
    }

    /**
     * Find post by Id
     *
     * @param int|null $id
     *
     * @return Post
     * @throws ValidationException
     */
    public function findOrFail($id = null)
    {
        $post = $this->post->find($id);

        if (!$post) {
            throw ValidationException::withMessages(['message' => trans('post.could_not_find')]);
        }

        return $post;
    }

    /**
     * Publish post or save draft.
     *
     * @param array $params
     *
     * @return Post
     * @throws ValidationException
     */
    public function create($params = [])
    {
        $id = isset($params['id']) ? $params['id'] : null;
        $is_draft = (isset($params['is_draft']) && $params['is_draft']) ? $params['is_draft'] : 0;

        if ($id) {
            $post = $this->post->filterById($id)->filterByUserId(\Auth::user()->id)->first();
            if (!$post) {
                throw ValidationException::withMessages(['message' => trans('post.invalid_action')]);
            }
        }
        $post = ($id) ? $this->post->find($id) : $this->post;
        $post->fill([
            'title' => $params['title'],
            'body' => $params['body']
        ]);

        if (!$id) {
            $post->user_id = \Auth::user()->id;
        }
        $post->is_draft = $is_draft;

        if (!$is_draft) {
            $post->created_at = Carbon::now();
        }
        $post->save();

        return $post;
    }

    /**
     * Get published posts.
     *
     * @return Post[]|Collection
     */
    public function getPublished()
    {
        return $this->post->filterByUserId(\Auth::user()->id)->filterByIsDraft(0)->get();
    }

    /**
     * Get draft posts.
     *
     * @return Post[]|Collection
     */
    public function getDraft()
    {
        return $this->post->filterByUserId(\Auth::user()->id)->filterByIsDraft(1)->get();
    }

    /**
     * Get draft posts.
     *
     * @param array $params
     *
     * @return Post[]|Collection
     */
    public function getDraftList($params = [])
    {
        $page_length = isset($params['page_length']) ? $params['page_length'] : config('config.page_length');
        $draft = $this->post->with('user', 'user.profile')->filterByUserId(\Auth::user()->id)->filterByIsDraft(1);
        if (!isset($params['page_length'])) {
            return $draft->get();
        }

        return $draft->orderBy('created_at', 'desc')->paginate($page_length);
    }

    /**
     * Get published posts.
     *
     * @param array $params
     *
     * @return Post[]|Collection
     */
    public function getPublishedList($params = [])
    {
        $page_length = isset($params['page_length']) ? $params['page_length'] : config('config.page_length');
        $published = $this->post->with('user', 'user.profile')->filterByUserId(\Auth::user()->id)->filterByIsDraft(0);

        if (!isset($params['page_length'])) {
            return $published->get();
        }

        return $published->orderBy('created_at', 'desc')->paginate($page_length);
    }
}
