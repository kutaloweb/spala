<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Repositories\LocaleRepository;
use App\Http\Requests\ConfigurationRequest;
use App\Repositories\ActivityLogRepository;
use App\Repositories\ConfigurationRepository;
use Illuminate\Support\Facades\File;

class ConfigurationController extends Controller
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var ConfigurationRepository
     */
    protected $repo;

    /**
     * @var ActivityLogRepository
     */
    protected $activity;

    /**
     * @var LocaleRepository
     */
    protected $locale;

    /**
     * @var string
     */
    protected $module = 'configuration';

    /**
     * Instantiate a new controller instance
     *
     * @param Request $request
     * @param ConfigurationRepository $repo
     * @param ActivityLogRepository $activity
     * @param LocaleRepository $locale
     */
    public function __construct(Request $request, ConfigurationRepository $repo, ActivityLogRepository $activity, LocaleRepository $locale)
    {
        $this->request = $request;
        $this->repo = $repo;
        $this->activity = $activity;
        $this->locale = $locale;

        $this->middleware('permission:access-configuration')->except('getConfigurationVariable', 'fetchList');
    }

    /**
     * Get configuration.
     *
     * @return array
     */
    public function index()
    {
        return $this->repo->getAll();
    }

    /**
     * Save configuration
     *
     * @param ConfigurationRequest $request
     *
     * @return JsonResponse
     */
    public function store(ConfigurationRequest $request)
    {
        $this->repo->create($this->request->all());

        $this->activity->record([
            'module' => $this->module,
            'sub_module' => request('config_type') ?: null,
            'activity' => 'saved'
        ]);

        return response()->json(['message' => trans('configuration.stored')]);
    }

    /**
     * Get configuration variables
     *
     * @return JsonResponse
     */
    public function getConfigurationVariable()
    {
        $system_variables = config('system');
        $color_themes = isset($system_variables['color_themes']) ? $system_variables['color_themes'] : [];
        $timezones = generateSelectValueOnly(config('timezone'));
        $locales = generateSelect($this->locale->list());
        $notification_positions = generateSelectVueTranslated(config('list.notification_positions'), 'select');
        return $this->success(compact('color_themes', 'notification_positions', 'timezones', 'locales'));
    }

    /**
     * Fetch list data
     *
     * @return JsonResponse
     */
    public function fetchList()
    {
        $lists = request('lists');
        $data = [];

        if (!$lists) {
            return $this->success(compact('data'));
        }

        $lists = explode(',', $lists);

        if (in_array('country', $lists)) {
            $data['country'] = generateSelect(config('country'));
            return $this->success(compact('data'));
        }

        if (in_array('timezone', $lists)) {
            $data['timezone'] = generateSelectValueOnly(config('timezone'));
            return $this->success(compact('data'));
        }

        $list_data = config('list');
        foreach ($lists as $list) {
            $list_item = isset($list_data[$list]) ? $list_data[$list] : [];
            $data[$list] = count($list_item) ? generateSelectVueTranslated($list_item) : [];
        }

        return $this->success(compact('data'));
    }

    /**
     * Upload logo or full screen background image
     *
     * @param string $type
     *
     * @return JsonResponse
     */
    public function uploadConfigImages($type)
    {
        $imagePath = config('system.upload_path.config') . '/' . $type . '/';
        $image = $this->repo->getByName($type);

        if ($image && File::exists($image->text_value)) {
            File::delete($image->text_value);
        }

        $extension = request()->file('image')->getClientOriginalExtension();
        $filename = uniqid();
        request()->file('image')->move($imagePath, $filename . "." . $extension);
        $img = \Image::make($imagePath . $filename . "." . $extension);
        $img->save($imagePath . $filename . "." . $extension);
        $config = $this->repo->firstOrCreate($type);
        $config->text_value = $imagePath . $filename . "." . $extension;
        $config->save();

        $this->activity->record([
            'module' => $this->module,
            'sub_module' => 'config_image',
            'activity' => 'uploaded'
        ]);

        return $this->success(['message' => trans('configuration.config_image_uploaded'), 'image' => $imagePath . $filename . "." . $extension]);
    }

    /**
     * Remove logo or full screen background image
     *
     * @param string $type
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function removeConfigImages($type)
    {
        $image = $this->repo->getByName($type);

        if ($image && File::exists($image->text_value)) {
            File::delete($image->text_value);
        }

        $this->repo->delete($type);

        $this->activity->record([
            'module' => $this->module,
            'sub_module' => 'config_image',
            'activity' => 'removed'
        ]);

        return $this->success(['message' => trans('configuration.config_image_removed')]);
    }
}
