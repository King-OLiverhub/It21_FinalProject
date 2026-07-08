<?php

use App\Models\User;
use App\Models\Server;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('the application redirects from / to /login', function () {
    $response = $this->get('/');
    $response->assertStatus(302);
    $response->assertRedirect('/login');
});

test('login page returns 200 status', function () {
    $response = $this->get('/login');
    $response->assertStatus(200);
});

test('administrator cannot access server creation page', function () {
    $admin = User::factory()->create([
        'role' => User::ROLE_ADMIN,
        'is_active' => true,
    ]);

    $response = $this->actingAs($admin)->get('/servers/create');
    $response->assertStatus(403);
});

test('regular user (security analyst) can access server creation page', function () {
    $user = User::factory()->create([
        'role' => User::ROLE_SECURITY_ANALYST,
        'is_active' => true,
    ]);

    $response = $this->actingAs($user)->get('/servers/create');
    $response->assertStatus(200);
});

test('administrator can access server edit page', function () {
    $admin = User::factory()->create([
        'role' => User::ROLE_ADMIN,
        'is_active' => true,
    ]);

    $server = Server::create([
        'name' => 'Test Server',
        'ip_address' => '127.0.0.1',
        'provider' => 'Local',
        'status' => 'online',
        'firewall_status' => 'Active',
    ]);

    $response = $this->actingAs($admin)->get(route('servers.edit', $server));
    $response->assertStatus(200);
});

test('regular user (security analyst) cannot access server edit page', function () {
    $user = User::factory()->create([
        'role' => User::ROLE_SECURITY_ANALYST,
        'is_active' => true,
    ]);

    $server = Server::create([
        'name' => 'Test Server',
        'ip_address' => '127.0.0.1',
        'provider' => 'Local',
        'status' => 'online',
        'firewall_status' => 'Active',
    ]);

    $response = $this->actingAs($user)->get(route('servers.edit', $server));
    $response->assertStatus(403);
});
