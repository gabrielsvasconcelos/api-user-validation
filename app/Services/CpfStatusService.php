<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class CpfStatusService
{
    public function getStatus(string $cpf): string
    {
        return $this->generateMockStatus($cpf);
    }

    private function generateMockStatus(string $cpf): string
    {
        $statuses = ['limpo', 'pendente', 'negativado'];
        $statusIndex = abs(crc32($cpf)) % count($statuses);
        
        $status = $statuses[$statusIndex];
        Log::debug("Generated mock CPF status", ['cpf' => $cpf, 'status' => $status]);
        
        return $status;
    }
}