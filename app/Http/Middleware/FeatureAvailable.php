<?php

namespace App\Http\Middleware;

use Closure;

class FeatureAvailable
{
    /**
     * Check whether a feature is available or not.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param string $feature
     *
     * @return mixed
     */
    public function handle($request, Closure $next, $feature)
    {
        if (!config('config.' . $feature) && \Auth()->check()) {
            return response()->json(['error' => 'feature_not_available'], 422);
        }

        return $next($request);
    }
}
