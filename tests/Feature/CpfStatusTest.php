<?php

use Illuminate\Support\Facades\Log;

test('mock cpf status returns valid response structure', function () {
    $cpf = '12345678909';
    $response = $this->getJson("/api/v1/mock/cpf-status/{$cpf}");

    $response->assertStatus(200)
        ->assertJsonStructure([
            'cpf',
            'status',
            'timestamp'
        ]);
});

test('mock cpf status returns one of the expected statuses', function () {
    $statuses = ['limpo', 'pendente', 'negativado'];
    $cpf = '98765432100';
    $response = $this->getJson("/api/v1/mock/cpf-status/{$cpf}");

    $response->assertStatus(200);
    $this->assertContains($response['status'], $statuses);
});

test('mock cpf status generates consistent status for same cpf', function () {
    $cpf = '11122233344';
    $firstResponse = $this->getJson("/api/v1/mock/cpf-status/{$cpf}");
    $secondResponse = $this->getJson("/api/v1/mock/cpf-status/{$cpf}");

    $this->assertEquals($firstResponse['status'], $secondResponse['status']);
});

test('mock cpf status logs debug information', function () {
    Log::spy();
    $cpf = '55566677788';
    
    $this->getJson("/api/v1/mock/cpf-status/{$cpf}");

    Log::shouldHaveReceived('debug')
        ->with("Generated CPF status", \Mockery::type('array'));
});

test('mock cpf status handles invalid cpf format', function () {
    $invalidCpfs = [
        '123',                   
        'abcdefghijk',           
        '123.456.789-09',        
        '11111111111',            
        '12345678901234567890'    
    ];

    foreach ($invalidCpfs as $cpf) {
        $response = $this->getJson("/api/v1/mock/cpf-status/{$cpf}");
        $response->assertStatus(200); 
    }
});

test('mock cpf status distribution is roughly even', function () {
    $statusCounts = [
        'limpo' => 0,
        'pendente' => 0,
        'negativado' => 0
    ];
    
    $iterations = 100;
    $cpfBase = '10000000000'; 
    
    for ($i = 0; $i < $iterations; $i++) {
        $cpf = (string)((int)$cpfBase + $i);
        $response = $this->getJson("/api/v1/mock/cpf-status/{$cpf}");
        $statusCounts[$response['status']]++;
    }
    
    foreach ($statusCounts as $status => $count) {
        $this->assertGreaterThan($iterations * 0.2, $count, 
            "Status $status apareceu apenas $count vezes em $iterations tentativas");
    }
});