<?php

use Tests\Helpers\TenantTestHelper;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);
uses(TenantTestHelper::class);

test('registration screen can be rendered', function () {
    $this->initializeTenant();
    $response = $this->get(route('register'));

    $response->assertStatus(200);
});

test('new users can register', function () {
    $this->initializeTenant();
    $response = $this->post(route('register.store'), [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
});
