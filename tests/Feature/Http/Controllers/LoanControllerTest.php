<?php

test('personal loan is available for all income levels', function () {
    $response = $this->postJson('/api/loan', [
        'customer' => [
            'name' => 'John Doe',
            'cpf' => '54967118003',
            'age' => 25,
            'location' => 'RJ',
            'income' => 2000
        ]
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'customer' => 'John Doe',
            'loans' => [
                ['type' => 'personal', 'taxes' => 1]
            ]
        ]);
});

test('collateralized loan for income <= 3000 requires age < 30 and SP location', function () {
    $response = $this->postJson('/api/loan', [
        'customer' => [
            'name' => 'Jane Doe',
            'cpf' => '54967118003',
            'age' => 25,
            'location' => 'SP',
            'income' => 3000
        ]
    ]);

    $response->assertStatus(200)
        ->assertJsonFragment(['type' => 'collateralized', 'taxes' => 3]);
});

test('collateralized loan for income 3000-5000 requires SP location only', function () {
    $response = $this->postJson('/api/loan', [
        'customer' => [
            'name' => 'Bob Smith',
            'cpf' => '54967118003',
            'age' => 35,
            'location' => 'SP',
            'income' => 4000
        ]
    ]);

    $response->assertStatus(200)
        ->assertJsonFragment(['type' => 'collateralized', 'taxes' => 3]);
});

test('collateralized loan for income >= 5000 requires age < 30 only', function () {
    $response = $this->postJson('/api/loan', [
        'customer' => [
            'name' => 'Alice Johnson',
            'cpf' => '54967118003',
            'age' => 25,
            'location' => 'RJ',
            'income' => 6000
        ]
    ]);

    $response->assertStatus(200)
        ->assertJsonFragment(['type' => 'collateralized', 'taxes' => 3]);
});

test('payroll loan requires income >= 5000', function () {
    $response = $this->postJson('/api/loan', [
        'customer' => [
            'name' => 'Rich Person',
            'cpf' => '54967118003',
            'age' => 30,
            'location' => 'RJ',
            'income' => 5000
        ]
    ]);

    $response->assertStatus(200)
        ->assertJsonFragment(['type' => 'payroll', 'taxes' => 2]);
});

test('validation fails with invalid data', function () {
    $response = $this->postJson('/api/loan', [
        'customer' => [
            'name' => '',
            'cpf' => '123',
            'age' => 15,
            'location' => 'XX',
            'income' => -1000
        ]
    ]);

    $response->assertStatus(422);
});

test('all loan types available for high income young person in SP', function () {
    $response = $this->postJson('/api/loan', [
        'customer' => [
            'name' => 'Perfect Customer',
            'cpf' => '54967118003',
            'age' => 25,
            'location' => 'SP',
            'income' => 6000
        ]
    ]);

    $response->assertStatus(200)
        ->assertJsonCount(3, 'loans')
        ->assertJsonFragment(['type' => 'personal'])
        ->assertJsonFragment(['type' => 'collateralized'])
        ->assertJsonFragment(['type' => 'payroll']);
});
