<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Return success response
     *
     * @param null $items
     * @param int $status
     *
     * @return JsonResponse
     */
    public function success($items = null, $status = 200)
    {
        $data = ['status' => 'success'];
        if ($items instanceof Arrayable) {
            $items = $items->toArray();
        }
        if ($items) {
            foreach ($items as $key => $item) {
                $data[$key] = $item;
            }
        }

        return response()->json($data, $status);
    }

    /**
     * Used to return error response
     *
     * @param null $items
     * @param int $status
     *
     * @return JsonResponse
     */
    public function error($items = null, $status = 422)
    {
        $data = [];
        if ($items) {
            foreach ($items as $key => $item) {
                $data['errors'][$key][] = $item;
            }
        }

        return response()->json($data, $status);
    }
}
