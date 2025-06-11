<?php

use App\Models\UserData;
use Illuminate\Support\Facades\Cache;

beforeEach(function () {
    UserData::truncate();
    Cache::flush();
});

it('returns 404 for non-existent user', function () {
    $response = $this->getJson('/api/v1/users/12345678901');
    
    $response->assertStatus(404)
        ->assertJson([
            'status' => 'error',
            'message' => 'User not found'
        ]);
});

it('returns user data from database when not cached', function () {
    $user = UserData::create([
        'cpf' => '11122233344',
        'cep' => '12345678',
        'email' => 'test1@example.com',
        'address_data' => json_encode(['logradouro' => 'Rua Teste 1']),
        'name_origin_data' => json_encode(['country' => 'BR']),
        'cpf_status' => 'limpo'
    ]);
    
    $response = $this->getJson("/api/v1/users/{$user->cpf}");
    
    $response->assertStatus(200)
        ->assertJson([
            'status' => 'database',
            'data' => [
                'cpf' => $user->cpf,
                'cep' => $user->cep,
                'email' => $user->email,
                'address' => ['logradouro' => 'Rua Teste 1'],
                'name_origin' => ['country' => 'BR'],
                'cpf_status' => 'limpo'
            ]
        ]);
});

it('returns user data from cache when available', function () {
    $cpf = '22233344455';
    
    $cachedData = [
        'cpf' => $cpf,
        'cep' => '12345678',
        'email' => 'cached@example.com',
        'address' => ['logradouro' => 'Rua Cache'],
        'name_origin' => ['country' => 'US'],
        'cpf_status' => 'pendente'
    ];
    
    Cache::put("user_data:{$cpf}", $cachedData);
    
    $response = $this->getJson("/api/v1/users/{$cpf}");
    
    $response->assertStatus(200)
        ->assertJson([
            'status' => 'cached',
            'data' => $cachedData
        ]);
});

it('uses redis tags when redis is cache driver', function () {
    config(['cache.default' => 'array']);
    
    $user = UserData::create([
        'cpf' => '33344455566',
        'cep' => '12345678',
        'email' => 'test2@example.com',
        'address_data' => json_encode(['logradouro' => 'Rua Teste 2']),
        'name_origin_data' => json_encode(['country' => 'BR']),
        'cpf_status' => 'limpo'
    ]);
    
    $response = $this->getJson("/api/v1/users/{$user->cpf}");
    $response->assertStatus(200);
    
    $this->assertTrue(Cache::has("user_data:{$user->cpf}"));
});