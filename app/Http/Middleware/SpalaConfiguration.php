<?php

namespace App\Http\Middleware;

use Closure;
use App\Repositories\ConfigurationRepository;

class SpalaConfiguration
{
    protected $config;

    public function __construct(ConfigurationRepository $config)
    {
        $this->config = $config;
    }

    /**
     * Set default configuration
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param  string|null $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $this->config->setConfiguration();

        return $next($request);
    }
}
