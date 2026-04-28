<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\VkApiException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\AnalyzeRequest;
use App\Http\Requests\Api\ReportRequest;
use App\Http\Resources\Api\ReportResource;
use App\Services\VkService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class AnalysisController extends Controller
{
    public function __construct(
        protected VkService $vkService
    ) {}

    public function analyze(AnalyzeRequest $request): JsonResponse
    {
        $validated = $request->validated();

        try {
            $data = $this->vkService->storeWallPosts($validated['group_id'], 100, $validated['from'], $validated['to']);

            return response()->json(['data' => $data]);
        } catch (VkApiException $e) {
            $httpStatus = match ($e->getCode()) {
                15 => 403,
                5 => 401,
                default => 400
            };

            return response()->json(['data' => ['message' => $e->getMessage()]], $httpStatus);
        } catch (Exception $e) {
            Log::error($e->getMessage());

            return response()->json(['data' => ['message' => 'Server error']], 500);
        }
    }

    public function report(ReportRequest $request, int|string $groupId): ReportResource|JsonResponse
    {
        $validated = $request->validated();

        try {
            $data = $this->vkService->getWallPosts($groupId, $validated['from'], $validated['to']);
        } catch (Exception $e) {
            return response()->json(['data' => ['message' => $e->getMessage()]], $e->getCode());
        }

        return new ReportResource($data);
    }
}
