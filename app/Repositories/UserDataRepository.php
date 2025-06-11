<?php

namespace App\Repositories;

use App\Models\UserData;
use Illuminate\Support\Facades\Log;

class UserDataRepository
{
    public function createOrUpdate(array $data): UserData
    {
        try {
            $userData = UserData::updateOrCreate(
                ['cpf' => $data['cpf']],
                [
                    'cep' => $data['cep'],
                    'email' => $data['email'],
                    'address_data' => $data['address'],
                    'name_origin_data' => $data['name_origin'],
                    'cpf_status' => $data['cpf_status'],
                ]
            );
            
            Log::info("User data saved", ['cpf' => $data['cpf']]);
            
            return $userData;
        } catch (\Exception $e) {
            Log::error("Failed to save user data", [
                'cpf' => $data['cpf'],
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
    
    public function findByCpf(string $cpf): ?UserData
    {
        return UserData::where('cpf', $cpf)->first();
    }
}