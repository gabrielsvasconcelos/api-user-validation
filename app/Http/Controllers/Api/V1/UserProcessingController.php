<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProcessUserDataRequest;
use App\Services\UserDataProcessingService;
use Illuminate\Http\JsonResponse;

class UserProcessingController extends Controller
{
    public function __construct(private UserDataProcessingService $processingService)
    {
    }

    public function process(ProcessUserDataRequest $request): JsonResponse
    {
        $validated = $request->validated();
        
        try {
            $result = $this->processingService->processUserData($validated);
            
            return response()->json([
                'status' => $result['status'],
                'data' => $result['data'],
            ], $result['status'] === 'cached' ? 200 : 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to process user data',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}