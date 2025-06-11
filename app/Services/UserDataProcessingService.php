<?php

namespace App\Services;

use App\Jobs\ProcessUserRiskAnalysis;
use App\Repositories\UserDataRepository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class UserDataProcessingService
{
    private const CACHE_TTL = 86400; 
    private const MAX_RETRIES = 3;

    public function __construct(
        private UserDataRepository $userDataRepository,
        private CpfStatusService $cpfStatusService
    ) {
    }

    public function processUserData(array $userData): array
    {
        $cacheKey = "user_data:{$userData['cpf']}";

        if (Cache::has($cacheKey)) {
            Log::info("Cache hit for user data", ['cpf' => $userData['cpf']]);
            return [
                'status' => 'cached',
                'data' => Cache::get($cacheKey)
            ];
        }

        Log::info("Processing new user data", ['cpf' => $userData['cpf']]);

        try {
            $externalData = $this->fetchExternalData($userData);
            $completeUserData = array_merge($userData, $externalData);

            $user = $this->userDataRepository->createOrUpdate($completeUserData);
            Cache::put($cacheKey, $completeUserData, self::CACHE_TTL);

            ProcessUserRiskAnalysis::dispatch($user);

            return [
                'status' => 'processed',
                'data' => $completeUserData
            ];
        } catch (Exception $e) {
            Log::error("Failed to process user data", [
                'cpf' => $userData['cpf'],
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    private function fetchExternalData(array $userData): array
    {
        $firstName = rawurlencode(explode('@', $userData['email'])[0]);

        $result = [
            'address' => [],
            'name_origin' => [],
            'cpf_status' => 'limpo'
        ];

        $this->fetchViaCepData($userData['cep'], $result);
        $this->fetchNationalizeData($firstName, $result);
        $result['cpf_status'] = $this->cpfStatusService->getStatus($userData['cpf']);

        return $result;
    }

    private function fetchViaCepData(string $cep, array &$result): void
    {
        $viaCepUrl = "https://viacep.com.br/ws/{$cep}/json";
        
        try {
            $viaCepResponse = Http::timeout(10)
                ->retry(self::MAX_RETRIES, 1000)
                ->get($viaCepUrl);

            if ($viaCepResponse->successful()) {
                $result['address'] = $viaCepResponse->json();
                Log::info("ViaCEP API response", [
                    'url' => $viaCepUrl,
                    'response' => $result['address'],
                    'status' => $viaCepResponse->status()
                ]);
            } else {
                Log::warning("ViaCEP API error", [
                    'url' => $viaCepUrl,
                    'status' => $viaCepResponse->status(),
                    'response' => $viaCepResponse->body()
                ]);
            }
        } catch (\Exception $e) {
            Log::error("ViaCEP API exception", [
                'url' => $viaCepUrl,
                'error' => $e->getMessage()
            ]);
            throw new \Exception("Failed to fetch address data: " . $e->getMessage());
        }
    }

    private function fetchNationalizeData(string $firstName, array &$result): void
    {
        $nationalizeUrl = "https://api.nationalize.io/?name={$firstName}";
        
        try {
            $nationalizeResponse = Http::timeout(10)
                ->retry(self::MAX_RETRIES, 1000)
                ->get($nationalizeUrl);

            if ($nationalizeResponse->successful()) {
                $result['name_origin'] = $nationalizeResponse->json();
                Log::info("Nationalize API response", [
                    'url' => $nationalizeUrl,
                    'response' => $result['name_origin'],
                    'status' => $nationalizeResponse->status()
                ]);
            } else {
                Log::warning("Nationalize API error", [
                    'url' => $nationalizeUrl,
                    'status' => $nationalizeResponse->status(),
                    'response' => $nationalizeResponse->body()
                ]);
            }
        } catch (\Exception $e) {
            Log::error("Nationalize API exception", [
                'url' => $nationalizeUrl,
                'error' => $e->getMessage()
            ]);
            throw new \Exception("Failed to fetch name origin data: " . $e->getMessage());
        }
    }
}