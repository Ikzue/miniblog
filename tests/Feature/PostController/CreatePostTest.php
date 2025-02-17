<?php

namespace Tests\Feature\PostController;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use App\Models\User;

class CreatePostTest extends TestCase
{
    use RefreshDatabase;

    private function authUser()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        return $user;
    }

    public function test_no_auth(): void
    {
        $response = $this->post('/api/posts', [
            'title' => 'My title',
            'content' => 'My content',
        ]);
        $response->assertRedirectToRoute('login');
    }

    public function test_auth(): void
    {
        $this->authUser();

        $response = $this->post('/api/posts', [
            'title' => 'My title',
            'content' => 'My content',
        ]);
        $response->assertRedirectToRoute('posts.list.ui');
    }

    public function test_create_OK(): void
    {
        $user = $this->authUser();
        $reqData = [
            'title' => 'My title',
            'content' => 'My content',
        ];
        $this->assertDatabaseCount('posts', 0);

        $response = $this->post('/api/posts', $reqData);
        $response->assertSessionHas('success', 'Post created successfully');
        $response->assertRedirect('/posts/list');

        $this->assertDatabaseCount('posts', 1);
        $this->assertDatabaseHas('posts', [
            'title' => $reqData['title'],
            'content' => $reqData['content'],
            'user_id' => $user->id
        ]);
    }

    public function test_create_missing_field_KO(): void
    {
        $this->authUser();
        $this->assertDatabaseCount('posts', 0);

        $response = $this->post('/api/posts', [
            'title' => 'My title',
        ]);
        $response->assertInvalid(['content']);

        $this->assertDatabaseCount('posts', 0);
    }

    public function test_get_disabled(): void
    {
        $this->authUser();
        $response = $this->get('/api/posts/create');
        $response->assertStatus(404);
    }
}
