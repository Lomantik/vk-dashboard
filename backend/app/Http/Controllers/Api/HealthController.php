<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class HealthController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @throws ConnectionException
     */
    public function index(): JsonResponse
    {
        $results = [
            'status' => 'UP',
            'timestamp' => now()->toIso8601String(),
            'services' => [
                'cache' => (Cache::put('health_check', 1, 10) && Cache::has('health_check')) ? 'ok' : 'fail',
                'vk_api' => Http::timeout(2)->get('https://vk.com')->ok() ? 'ok' : 'down',
            ],
        ];
        if (in_array('down', $results['services']) || in_array('fail', $results['services'])) {
            $results['status'] = 'DOWN';

            return response()->json($results, 503);
        }

        return response()->json($results);
    }
}
