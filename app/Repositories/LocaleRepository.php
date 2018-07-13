<?php

namespace App\Repositories;

use App\Locale;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\ValidationException;

class LocaleRepository
{
    /**
     * @var Locale
     */
    protected $locale;

    /**
     * Instantiate a new Repository instance.
     *
     * @param Locale $locale
     */
    public function __construct(Locale $locale)
    {
        $this->locale = $locale;
    }

    /**
     * Get all locales.
     *
     * @return array
     */
    public function getAll()
    {
        return $this->locale->all();
    }

    /**
     * List all locales.
     *
     * @return array
     */
    public function list()
    {
        return $this->locale->all()->pluck('locale_with_name', 'locale')->all();
    }

    /**
     * Find locale with given id.
     *
     * @param int $id
     *
     * @return Locale
     * @throws ValidationException
     */
    public function findOrFail($id)
    {
        $locale = $this->locale->find($id);

        if (!$locale) {
            throw ValidationException::withMessages(['message' => trans('locale.could_not_find')]);
        }

        return $locale;
    }

    /**
     * Find by locale.
     *
     * @param $locale
     *
     * @return Locale
     */
    public function findByLocale($locale)
    {
        return $this->locale->filterByLocale($locale)->first();
    }

    /**
     * Paginate all locales using given params.
     *
     * @param array $params
     *
     * @return LengthAwarePaginator
     */
    public function paginate($params)
    {
        $sort_by = isset($params['sort_by']) ? $params['sort_by'] : 'name';
        $order = isset($params['order']) ? $params['order'] : 'asc';
        $page_length = isset($params['page_length']) ? $params['page_length'] : config('config.page_length');

        return $this->locale->orderBy($sort_by, $order)->paginate($page_length);
    }

    /**
     * Create a new locale.
     *
     * @param array $params
     *
     * @return Locale
     */
    public function create($params)
    {
        $locale = $this->locale->forceCreate($this->formatParams($params));
        $newDir = base_path('/resources/lang/' . $params['locale']);

        if ($locale->locale !== 'en') {
            File::copyDirectory(base_path('/resources/lang/en'), $newDir);
            recursiveChmod($newDir, 0777, 0777);
        }

        return $locale;
    }

    /**
     * Create a new locale if it does not exist.
     *
     * @param array $params
     *
     * @return Locale
     */
    public function firstOrCreate($params)
    {
        $locale = $this->locale->firstOrCreate($params);

        return $locale;
    }

    /**
     * Prepare given params for inserting into database.
     *
     * @param array $params
     * @param string $action
     *
     * @return array
     */
    private function formatParams($params, $action = 'create')
    {
        $formatted = [
            'name' => isset($params['name']) ? $params['name'] : null
        ];

        if ($action === 'create') {
            $formatted['locale'] = isset($params['locale']) ? $params['locale'] : null;
        }

        return $formatted;
    }

    /**
     * Update given locale.
     *
     * @param Locale $locale
     * @param array $params
     *
     * @return Locale
     * @throws ValidationException
     */
    public function update(Locale $locale, $params)
    {
        if ($locale->locale != $params['locale']) {
            throw ValidationException::withMessages(['locale' => trans('locale.locale_cannot_be_changed')]);
        }

        $locale->forceFill($this->formatParams($params, 'update'))->save();

        return $locale;
    }

    /**
     * Find locale and check if it can be deleted or not.
     *
     * @param int $id
     *
     * @return Locale
     * @throws ValidationException
     */
    public function deletable($id)
    {
        $locale = $this->findOrFail($id);

        if ($locale->locale === 'en') {
            throw ValidationException::withMessages(['message' => trans('locale.default_cannot_be_deleted')]);
        }

        return $locale;
    }

    /**
     * Delete locale.
     *
     * @param Locale $locale
     *
     * @return bool|null
     * @throws \Exception
     */
    public function delete(Locale $locale)
    {
        File::deleteDirectory(base_path('/resources/lang/' . $locale->locale));

        return $locale->delete();
    }

    /**
     * Delete multiple locales.
     *
     * @param array $ids
     *
     * @return bool|null
     */
    public function deleteMultiple($ids)
    {
        return $this->locale->whereIn('id', $ids)->delete();
    }

    /**
     * Toggle given locale status.
     *
     * @param Locale $locale
     *
     * @return Locale
     */
    public function toggle(Locale $locale)
    {
        $locale->forceFill([
            'completed_at' => (!$locale->status) ? Carbon::now() : null,
            'status' => !$locale->status
        ])->save();

        return $locale;
    }

    /**
     * Get all locale modules.
     *
     * @return array
     */
    public function getModules()
    {
        $modules = [];
        foreach (File::allFiles(base_path('/resources/lang/en')) as $file) {
            $modules[] = basename($file, '.php');
        }

        return $modules;
    }
}
