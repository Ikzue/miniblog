<?php

namespace Tests\Feature\PostController;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use App\Models\Post;
use App\Models\Comment;
use App\Models\User;

class DeletePostTest extends TestCase
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
        $user = User::factory()->create();
        $post = Post::factory()->for($user)->create();

        $response = $this->delete("/api/posts/{$post->id}");
        $response->assertRedirectToRoute('login');
        $this->assertDatabaseCount('posts', 1);
    }

    public function test_can_delete_post_without_side_effects(): void
    {
        $user = $this->authUser();
        $post = Post::factory()->for($user)->create();
        Comment::factory()->count(2)->for($user)->for($post)->create();

        $response = $this->delete("/api/posts/{$post->id}");
        $response->assertStatus(204);

        $this->assertDatabaseCount('posts', 0);
        $this->assertDatabaseCount('comments', 0);
    }

    public function test_cannot_delete_other_user_post(): void
    {
        $this->authUser();
        $otherUser = User::factory()->create();
        $post = Post::factory()->for($otherUser)->create();
        Comment::factory()->count(2)->for($otherUser)->for($post)->create();

        $response = $this->delete("/api/posts/{$post->id}");
        $response->assertStatus(403);

        $this->assertDatabaseCount('posts', 1);
        $this->assertDatabaseCount('comments', 2);
    }
}
