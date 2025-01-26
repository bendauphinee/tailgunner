<?php

namespace Tests\Unit\Controllers;

use App\Models\User;
use App\Models\Template;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;

class TemplateControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_index_returns_user_templates()
    {
        // Create templates for current user
        Template::factory()->count(3)->create([
            'user_id' => $this->user->id
        ]);

        // Create templates for another user (shouldn't be returned)
        Template::factory()->count(2)->create();

        $response = $this->actingAs($this->user)
            ->get('/templates');

        $response->assertInertia(fn (Assert $page) => $page
            ->component('Templates/Index')
            ->has('templates', 3, fn (Assert $page) => $page
                ->has('id')
                ->has('title')
                ->has('description')
                ->has('created_at')
                ->has('last_used')
                ->has('records')
            )
        );
    }

    public function test_store_creates_new_template()
    {
        $response = $this->actingAs($this->user)
            ->post('/templates', [
                'title' => 'Test Template'
            ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('templates', [
            'title' => 'Test Template',
            'user_id' => $this->user->id
        ]);
    }

    public function test_store_validates_title_length()
    {
        $response = $this->actingAs($this->user)
            ->postJson('/templates', [
                'title' => str_repeat('a', 121)
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title']);
        $this->assertDatabaseCount('templates', 0);
    }

    public function test_store_validates_empty_title()
    {
        $response = $this->actingAs($this->user)
            ->postJson('/templates', [
                'title' => ''
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title']);
    }

    public function test_index_requires_authentication()
    {
        $response = $this->get('/templates');
        $response->assertRedirect('/login');
    }

    public function test_store_requires_authentication()
    {
        $response = $this->post('/templates', [
            'title' => 'Test Template'
        ]);

        $response->assertRedirect('/login');
        $this->assertDatabaseCount('templates', 0);
    }

    public function test_store_returns_correctly_formatted_response()
    {
        $response = $this->actingAs($this->user)
            ->post('/templates', [
                'title' => 'Test Template'
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'template' => [
                    'id',
                    'title',
                    'description',
                    'created_at',
                    'last_used',
                    'records'
                ],
                'flash' => [
                    'success' => [
                        'data' => [
                            'id',
                            'title'
                        ]
                    ]
                ]
            ]);
    }
}
