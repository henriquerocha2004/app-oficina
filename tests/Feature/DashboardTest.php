<?php

use App\Models\User;
use Tests\Helpers\TenantTestHelper;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);
uses(TenantTestHelper::class);

test('guests are redirected to the login page', function () {
    $this->initializeTenant();
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('authenticated users can visit the dashboard', function () {
    $this->initializeTenant();
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertStatus(200);
});
