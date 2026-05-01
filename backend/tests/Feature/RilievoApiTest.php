<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RilievoApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_login_returns_token_and_user(): void
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'manager@rilievo.io',
            'password' => 'Rilievo123!',
        ]);

        $response
            ->assertOk()
            ->assertJsonStructure([
                'token',
                'user' => ['id', 'name', 'email', 'role'],
            ]);
    }

    public function test_dashboard_and_lookups_require_and_accept_authentication(): void
    {
        $this->getJson('/api/dashboard')->assertUnauthorized();

        $token = $this->postJson('/api/auth/login', [
            'email' => 'manager@rilievo.io',
            'password' => 'Rilievo123!',
        ])->json('token');

        $headers = ['Authorization' => "Bearer {$token}"];

        $this->getJson('/api/dashboard', $headers)
            ->assertOk()
            ->assertJsonStructure([
                'active_projects',
                'permits_at_risk',
                'open_issues',
                'contractors_pending',
                'projects',
                'alerts',
                'diary_highlights',
            ]);

        $this->getJson('/api/lookups', $headers)
            ->assertOk()
            ->assertJsonStructure([
                'users',
                'projects',
                'project_statuses',
                'permit_statuses',
                'issue_priorities',
                'issue_statuses',
                'contractor_statuses',
            ]);
    }

    public function test_authenticated_user_can_create_issue(): void
    {
        $token = $this->postJson('/api/auth/login', [
            'email' => 'coordinator@rilievo.io',
            'password' => 'Rilievo123!',
        ])->json('token');

        $response = $this->postJson('/api/issues', [
            'project_id' => 1,
            'category' => 'Safety',
            'priority' => 'HIGH',
            'status' => 'OPEN',
            'title' => 'Temporary fencing gap',
            'description' => 'North access fence needs immediate reinforcement.',
            'assignee_name' => 'Lorenzo Ferri',
            'due_date' => now()->addDays(1)->toDateString(),
        ], [
            'Authorization' => "Bearer {$token}",
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('title', 'Temporary fencing gap')
            ->assertJsonPath('raised_by.id', 2);

        $this->assertDatabaseHas('issues', [
            'title' => 'Temporary fencing gap',
            'category' => 'Safety',
        ]);
    }
}
