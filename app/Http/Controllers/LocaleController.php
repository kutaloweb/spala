<?php

namespace App\Http\Controllers;

use App\Locale;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\LocaleRequest;
use App\Repositories\LocaleRepository;
use App\Repositories\ActivityLogRepository;
use Illuminate\Validation\ValidationException;

class LocaleController extends Controller
{
    /**
     * @var LocaleRepository
     */
    protected $repo;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var ActivityLogRepository
     */
    protected $activity;

    /**
     * @var string
     */
    protected $module = 'locale';

    /**
     * Instantiate a new controller instance
     *
     * @param Request $request
     * @param LocaleRepository $repo
     * @param ActivityLogRepository $activity
     */
    public function __construct(Request $request, LocaleRepository $repo, ActivityLogRepository $activity)
    {
        $this->repo = $repo;
        $this->request = $request;
        $this->activity = $activity;

        $this->middleware('permission:access-configuration');
        $this->middleware('feature.available:multilingual');
    }

    /**
     * Get all locales
     *
     * @return JsonResponse
     */
    public function index()
    {
        $locales = $this->repo->paginate($this->request->all());

        $modules = $this->repo->getModules();

        return $this->success(compact('locales', 'modules'));
    }

    /**
     * Store locale
     *
     * @param LocaleRequest $request
     *
     * @return JsonResponse
     */
    public function store(LocaleRequest $request)
    {
        $this->repo->create($this->request->all());

        $this->activity->record([
            'module' => $this->module,
            'activity' => 'added'
        ]);

        return $this->success(['message' => trans('locale.added')]);
    }

    /**
     * Get locale details
     *
     * @param int $id
     *
     * @return Locale
     * @throws ValidationException
     */
    public function show($id)
    {
        return $this->repo->findOrFail($id);
    }

    /**
     * Update locale
     *
     * @param LocaleRequest $request
     * @param int $id
     *
     * @return JsonResponse
     * @throws ValidationException
     */
    public function update(LocaleRequest $request, $id)
    {
        $locale = $this->repo->findOrFail($id);

        $this->repo->update($locale, $this->request->all());

        $this->activity->record([
            'module' => $this->module,
            'activity' => 'updated'
        ]);

        return $this->success(['message' => trans('locale.updated')]);
    }

    /**
     * Delete locale
     *
     * @param int $id
     *
     * @return JsonResponse
     * @throws ValidationException
     * @throws \Exception
     */
    public function destroy($id)
    {
        $locale = $this->repo->deletable($id);

        $this->activity->record([
            'module' => $this->module,
            'activity' => 'deleted'
        ]);

        $this->repo->delete($locale);

        return $this->success(['message' => trans('locale.deleted')]);
    }
}
