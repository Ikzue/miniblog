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

    public function test_should_redirect_guest(): void
    {
        $response = $this->post('/api/posts', [
            'title' => 'My title',
            'content' => 'My content',
        ]);
        $response->assertRedirectToRoute('login');
    }

    public function test_should_redirect_to_posts_list_after_post_creation(): void
    {
        $this->authUser();

        $response = $this->post('/api/posts', [
            'title' => 'My title',
            'content' => 'My content',
        ]);
        $response->assertRedirectToRoute('posts.list.ui');
    }

    public function test_can_create_post(): void
    {
        $user = $this->authUser();
        $this->assertDatabaseCount('posts', 0);

        $response = $this->post('/api/posts', [
            'title' => 'My title',
            'content' => 'My content',
        ]);
        $response->assertSessionHas('success', 'Post created successfully');
        $response->assertRedirect('/posts/list');

        $this->assertDatabaseCount('posts', 1);
        $this->assertDatabaseHas('posts', [
            'title' => 'My title',
            'content' => 'My content',
            'user_id' => $user->id
        ]);
    }

    public function test_cannot_create_post_with_missing_field(): void
    {
        $this->authUser();
        $this->assertDatabaseCount('posts', 0);

        $response = $this->post('/api/posts', [
            'title' => 'My title',
        ]);
        $response->assertInvalid(['content']);

        $this->assertDatabaseCount('posts', 0);
    }
}
