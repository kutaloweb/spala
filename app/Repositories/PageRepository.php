<?php

namespace App\Repositories;

use App\Page;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class PageRepository
{
    /**
     * @var Page
     */
    protected $page;

    /**
     * Instantiate a new controller instance.
     *
     * @param Page $page
     */
    public function __construct(Page $page)
    {
        $this->page = $page;
    }

    /**
     * Count pages
     *
     * @return int
     */
    public function count()
    {
        return $this->page->count();
    }

    /**
     * Get valid page by slug.
     *
     * @param string $slug
     *
     * @return Page
     * @throws ValidationException
     */
    public function getBySlug($slug)
    {
        $page = $this->page
            ->filterBySlug($slug)
            ->first();

        if (!$page) {
            throw ValidationException::withMessages(['message' => trans('general.invalid_link')]);
        }

        return $page;
    }

    /**
     * Get valid page by slug for guests.
     *
     * @param string $slug
     *
     * @return Page
     */
    public function getBySlugForGuests($slug)
    {
        $page = $this->page
            ->filterBySlug($slug)
            ->first();

        if (!$page) {
            return null;
        }

        return $page;
    }

    /**
     * Find page by Id
     *
     * @param int|null $id
     *
     * @return Page
     * @throws ValidationException
     */
    public function findOrFail($id = null)
    {
        $page = $this->page->find($id);

        if (!$page) {
            throw ValidationException::withMessages(['message' => trans('page.could_not_find')]);
        }

        return $page;
    }

    /**
     * Publish page.
     *
     * @param array $params
     *
     * @return Page
     * @throws ValidationException
     */
    public function create($params = [])
    {
        $id = isset($params['id']) ? $params['id'] : null;

        if ($id) {
            $page = $this->page->filterById($id)->first();
            if (!$page) {
                throw ValidationException::withMessages(['message' => trans('page.invalid_action')]);
            }
        }
        $page = ($id) ? $this->page->find($id) : $this->page;
        $page->fill([
            'title' => $params['title'],
            'body' => $params['body'],
        ]);

        if (!$id) {
            $slug = str_slug($params['title']);
            if (Page::whereSlug($slug)->exists()) {
                throw ValidationException::withMessages(['message' => trans('page.slug_exists')]);
            }
            $page->fill([
                'slug' => $slug,
            ]);
        }

        $page->save();

        return $page;
    }

    /**
     * Get published pages.
     *
     * @return Page[]|Collection
     */
    public function getPublished()
    {
        return $this->page->get();
    }

    /**
     * Get published pages.
     *
     * @param array $params
     *
     * @return Page[]|Collection
     */
    public function getPublishedList($params = [])
    {
        $page_length = isset($params['page_length']) ? $params['page_length'] : config('config.page_length');

        $published = $this->page;

        if (!isset($params['page_length'])) {
            return $published->get();
        }

        return $published->orderBy('created_at', 'desc')->paginate($page_length);
    }
}
