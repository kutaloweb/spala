<?php

namespace App\Repositories;

use App\Category;
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
     * Get published posts.
     *
     * @param array $params
     *
     * @return Post[]|Collection
     */
    public function getPosts($params = [])
    {
        $page_length = isset($params['page_length']) ? $params['page_length'] : config('config.page_length');
        $search_query = isset($params['search_query']) ? $params['search_query'] : null;
        $category_id = isset($params['category_id']) ? $params['category_id'] : null;
        $created_at_start_date = isset($params['created_at_start_date']) ? $params['created_at_start_date'] : null;
        $created_at_end_date = isset($params['created_at_end_date']) ? $params['created_at_end_date'] : null;

        $published = $this->post
            ->with('user', 'user.profile', 'category')
            ->filterByIsDraft(0)
            ->filterBySearchQuery($search_query)
            ->filterByCategoryId($category_id)
            ->createdAtDateBetween([
                'start_date' => $created_at_start_date,
                'end_date' => $created_at_end_date
            ]);

        if (!isset($params['page_length'])) {
            return $published->get();
        }

        return $published->orderBy('created_at', 'desc')->paginate($page_length);
    }

    /**
     * Get post query.
     *
     * @return Post
     */
    public function getQuery()
    {
        return $this->post->with('user', 'user.profile')->filterByIsDraft(0);
    }

    /**
     * Count posts
     *
     * @return int
     */
    public function count()
    {
        return $this->post->filterByIsDraft(0)->count();
    }

    /**
     * Count post published between dates
     *
     * @param string $start_date
     * @param string $end_date
     *
     * @return int
     */
    public function countDateBetween($start_date, $end_date)
    {
        return $this->post->filterByIsDraft(0)->createdAtDateBetween(['start_date' => $start_date, 'end_date' => $end_date])->count();
    }

    /**
     * Get valid post by slug.
     *
     * @param string $slug
     *
     * @return Post
     * @throws ValidationException
     */
    public function getBySlug($slug)
    {
        $post = $this->post->with('user', 'user.profile', 'category')
            ->filterBySlug($slug)
            ->filterByUserId(\Auth::user()->id)
            ->first();

        if (!$post) {
            throw ValidationException::withMessages(['message' => trans('general.invalid_link')]);
        }

        return $post;
    }

    /**
     * Get valid post by category and slug.
     *
     * @param string $category
     * @param string $slug
     *
     * @return Post
     */
    public function getByCategoryAndSlug($category, $slug)
    {
        $category = Category::where('slug', $category)->first();

        if (!$category) {
            return null;
        }

        $post = $this->post->with('user', 'user.profile', 'category')
            ->filterByCategoryAndSlug($category, $slug)
            ->first();

        if (!$post) {
            return null;
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
            'body' => $params['body'],
            'category_id' => $params['category_id'],
        ]);

        if (!$id) {
            $post->user_id = \Auth::user()->id;
            $post->setUniqueSlug($params['title']);
        }
        $post->is_draft = $is_draft;

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
        $search_query = isset($params['search_query']) ? $params['search_query'] : null;
        $category_id = isset($params['category_id']) ? $params['category_id'] : null;
        $created_at_start_date = isset($params['created_at_start_date']) ? $params['created_at_start_date'] : null;
        $created_at_end_date = isset($params['created_at_end_date']) ? $params['created_at_end_date'] : null;

        $draft = $this->post
            ->with('user', 'user.profile', 'category')
            ->filterByUserId(\Auth::user()->id)
            ->filterByIsDraft(1)
            ->filterBySearchQuery($search_query)
            ->filterByCategoryId($category_id)
            ->createdAtDateBetween([
                'start_date' => $created_at_start_date,
                'end_date' => $created_at_end_date
            ]);

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
        $search_query = isset($params['search_query']) ? $params['search_query'] : null;
        $category_id = isset($params['category_id']) ? $params['category_id'] : null;
        $created_at_start_date = isset($params['created_at_start_date']) ? $params['created_at_start_date'] : null;
        $created_at_end_date = isset($params['created_at_end_date']) ? $params['created_at_end_date'] : null;

        $published = $this->post
            ->with('user', 'user.profile', 'category')
            ->filterByUserId(\Auth::user()->id)
            ->filterByIsDraft(0)
            ->filterBySearchQuery($search_query)
            ->filterByCategoryId($category_id)
            ->createdAtDateBetween([
                'start_date' => $created_at_start_date,
                'end_date' => $created_at_end_date
            ]);

        if (!isset($params['page_length'])) {
            return $published->get();
        }

        return $published->orderBy('created_at', 'desc')->paginate($page_length);
    }
}
