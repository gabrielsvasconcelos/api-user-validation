<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Repositories\UserDataRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class UserDataController extends Controller
{
    public function __construct(private UserDataRepository $userDataRepository)
    {
    }

    public function show(string $cpf): JsonResponse
    {
        $cacheKey = "user_data:{$cpf}";
        
        if (Cache::has($cacheKey)) {
            return response()->json([
                'status' => 'cached',
                'data' => Cache::get($cacheKey),
            ]);
        }
        
        $userData = $this->userDataRepository->findByCpf($cpf);
        
        if (!$userData) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found',
            ], 404);
        }
        
        $data = [
            'cpf' => $userData->cpf,
            'cep' => $userData->cep,
            'email' => $userData->email,
            'address' => json_decode($userData->address_data, true),
            'name_origin' => json_decode($userData->name_origin_data, true),
            'cpf_status' => $userData->cpf_status,
        ];
        
        if (config('cache.default') === 'redis') {
            Cache::tags(['user_data'])->put($cacheKey, $data, 86400);
        } else {
            Cache::put($cacheKey, $data, 86400);
        }
        
        return response()->json([
            'status' => 'database',
            'data' => $data,
        ]);
    }
}