<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class CpfStatusController extends Controller
{
    public function __construct()
    {
    }

    public function check(string $cpf): JsonResponse
    {
        $status = $this->generateMockStatus($cpf);
        
        return response()->json([
            'cpf' => $cpf,
            'status' => $status,
            'timestamp' => now()->toDateTimeString()
        ]);
    }

    private function generateMockStatus(string $cpf): string
    {
        $statuses = ['limpo', 'pendente', 'negativado'];
        $lastDigits = substr($cpf, -2);
        $statusIndex = ((int)$lastDigits) % count($statuses);
        $statusIndex = max(0, min($statusIndex, count($statuses) - 1));
        
        Log::debug("Generated CPF status", [
            'cpf' => $cpf,
            'last_digits' => $lastDigits,
            'status_index' => $statusIndex,
            'status' => $statuses[$statusIndex]
        ]);
        
        return $statuses[$statusIndex];
    }
}