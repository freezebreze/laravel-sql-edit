<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DevSqlPaginationTest extends TestCase
{
    use RefreshDatabase;

    public function test_select_results_are_paginated_on_the_server(): void
    {
        $user = User::factory()->create();
        User::factory()->count(24)->create();

        $response = $this->actingAs($user)->postJson(route('dev.execute'), [
            'sql' => 'SELECT id, email FROM users ORDER BY id',
            'page' => 2,
            'per_page' => 20,
        ]);

        $response
            ->assertOk()
            ->assertJsonCount(5, 'data')
            ->assertJsonPath('pagination.page', 2)
            ->assertJsonPath('pagination.per_page', 20)
            ->assertJsonPath('pagination.total', 25)
            ->assertJsonPath('pagination.last_page', 2);
    }

    public function test_a_limit_in_the_user_sql_caps_the_total_result(): void
    {
        $user = User::factory()->create();
        User::factory()->count(24)->create();

        $response = $this->actingAs($user)->postJson(route('dev.execute'), [
            'sql' => 'SELECT id FROM users ORDER BY id LIMIT 10;',
            'page' => 2,
            'per_page' => 10,
        ]);

        $response
            ->assertOk()
            ->assertJsonCount(0, 'data')
            ->assertJsonPath('pagination.total', 10)
            ->assertJsonPath('pagination.last_page', 1);
    }
}
