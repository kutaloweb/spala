<?php

namespace App\Repositories;

use App\Configuration;
use Carbon\Carbon;
use File;

class ConfigurationRepository
{
    /**
     * @var Configuration
     */
    protected $config;

    /**
     * Instantiate a new Repository instance
     *
     * @param Configuration $config
     */
    public function __construct(Configuration $config)
    {
        $this->config = $config;
    }

    /**
     * Get all config variables
     *
     * @return array
     */
    public function getAll()
    {
        return $this->config->all()->pluck('value', 'name')->all();
    }

    /**
     * Get config variable by name
     *
     * @param $names
     *
     * @return mixed
     */
    public function getByName($names)
    {
        return $this->config->filterByName($names)->first();
    }

    /**
     * Get selected config variables by name
     *
     * @return array
     */
    public function getSelectedByName($names)
    {
        return $this->config->whereIn('name', $names)->get()->pluck('value', 'name')->all();
    }

    /**
     * Find configuration by name else create
     *
     * @param string $name
     *
     * @return Configuration
     */
    public function firstOrCreate($name)
    {
        return $this->config->firstOrCreate(['name' => $name]);
    }

    /**
     * Store a configuration
     *
     * @param string $name
     * @param mixed $value
     * @param int $private
     *
     * @return Configuration
     */
    public function set($name, $value, $private = 0)
    {
        return $this->config->firstOrCreate([
            'name' => $name,
            'text_value' => ($value) ? !is_numeric($value) ? $value : null : null,
            'numeric_value' => is_numeric($value) ? $value : null,
        ]);
    }

    /**
     * Delete a configuration
     *
     * @param string $name
     *
     * @return bool|null
     * @throws \Exception
     */
    public function delete($name)
    {
        return $this->config->where('name', $name)->delete();
    }

    /**
     * Store configuration.
     *
     * @param array $params
     */
    public function create($params)
    {
        foreach ($params as $key => $value) {
            $value = (is_array($value)) ? implode(',', $value) : $value;
            $config = $this->firstOrCreate($key);
            $config->numeric_value = is_numeric($value) ? $value : null;
            $config->text_value = !is_numeric($value) ? $value : null;
            $config->save();
        }

        if (!empty($params['color_theme'])) $this->setDefaultCover($params['color_theme']);
        $this->setLocale($params);
    }

    /**
     * Store locale configuration.
     *
     * @param array $params
     */
    public function setLocale($params)
    {
        $config_type = isset($params['config_type']) ? $params['config_type'] : null;
        $locale = isset($params['locale']) ? $params['locale'] : config('app.locale');

        if ($config_type !== 'system') {
            return;
        }

        if ($locale === config('app.locale')) {
            return;
        }

        config(['app.locale' => $locale]);
        \App::setLocale(config('app.locale'));
        \Cache::forget('lang.js');
    }

    /**
     * Set configuration variables.
     */
    public function setConfiguration()
    {
        $system_variables = config('system');
        $default_config = isset($system_variables['default_config']) ? $system_variables['default_config'] : [];
        foreach ($default_config as $key => $value) {
            $config = $this->firstOrCreate($key);
            if (!is_numeric($config->numeric_value) && ($config->value === '' || $config->value === null)) {
                $config->numeric_value = is_numeric($value) ? $value : null;
                $config->text_value = !is_numeric($value) ? $value : null;
                $config->save();
            }
        }
        config(['config' => $this->getAll()]);
        config(['system' => $system_variables]);
        date_default_timezone_set(config('config.timezone'));
        \App::setLocale(config('config.locale') ?: 'en');
        Carbon::setLocale(config('config.locale') ?: 'en');
    }

    /**
     * Set default cover color when changing theme.
     *
     * @param string $color_theme
     */
    protected function setDefaultCover($color_theme)
    {
        switch ($color_theme) {
            case "blue":
            case "blue-dark":
            case "default":
            case "default-dark":
                File::copy(public_path() . '/images/cover-default-blue.png', public_path() . '/uploads/images/cover-default.png');
                break;
            case "green":
            case "green-dark":
                File::copy(public_path() . '/images/cover-default-green.png', public_path() . '/uploads/images/cover-default.png');
                break;
            case "megna":
            case "megna-dark":
                File::copy(public_path() . '/images/cover-default-megna.png', public_path() . '/uploads/images/cover-default.png');
                break;
            case "purple":
            case "purple-dark":
                File::copy(public_path() . '/images/cover-default-purple.png', public_path() . '/uploads/images/cover-default.png');
                break;
            case "red":
            case "red-dark":
                File::copy(public_path() . '/images/cover-default-red.png', public_path() . '/uploads/images/cover-default.png');
                break;
            default:
                File::copy(public_path() . '/images/cover-default-red.png', public_path() . '/uploads/images/cover-default.png');
        }
    }
}
