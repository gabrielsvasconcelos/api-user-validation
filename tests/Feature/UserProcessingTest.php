<?php

use Illuminate\Support\Facades\Http;

test('process user with valid data', function () {
    Http::fake([
        'viacep.com.br/*' => Http::response([
            'cep' => '06454000',
            'logradouro' => 'Rua Teste',
            'localidade' => 'São Paulo',
            'uf' => 'SP'
        ], 200),
        'api.nationalize.io/*' => Http::response([
            'name' => 'teste',
            'country' => [['country_id' => 'BR', 'probability' => 0.9]]
        ], 200),
        '*/mock/cpf-status/*' => Http::response([
            'cpf' => '12345678900',
            'status' => 'limpo'
        ], 200)
    ]);

    $response = $this->postJson('/api/v1/users/process', [
        'cpf' => '60414528309',
        'cep' => '62630000',
        'email' => 'gabriel@gmail.com'
    ]);

    $response->assertStatus(201);
});

test('process user with invalid data', function () {
    $response = $this->postJson('/api/v1/users/process', [
        'cpf' => '123',
        'cep' => '06454',
        'email' => 'não-é-email'
    ]);

    $response->assertStatus(422);
});

test('get user data from cache', function () {
    $this->app['config']->set('cache.default', 'array'); 
    
    $data = [
        'cpf' => '12345678900',
        'cep' => '06454000',
        'email' => 'teste@example.com',
        'address' => [],
        'name_origin' => [],
        'cpf_status' => 'limpo'
    ];

    cache()->put('user_data:12345678900', $data);

    $response = $this->getJson('/api/v1/users/12345678900');

    $response->assertStatus(200);
});