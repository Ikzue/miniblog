<?php

namespace Tests\Feature\PostController;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use App\Models\Post;
use App\Models\Comment;
use App\Models\User;
use App\Enums\Role;

class DeletePostTest extends TestCase
{
    use RefreshDatabase;

    private function authUser(Role $role = Role::READER)
    {
        $user = User::factory()->role($role)->create();
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

    public function test_can_delete_own_post_without_side_effects(): void
    {
        $user = $this->authUser(Role::MODERATOR);
        $post = Post::factory()->for($user)->create();
        $anotherPost = Post::factory()->for($user)->create();
        Comment::factory()->count(2)->for($user)->for($post)->create();
        Comment::factory()->count(3)->for($user)->for($anotherPost)->create();

        $this->assertDatabaseCount('posts', 2);
        $this->assertDatabaseCount('comments', 5);

        $response = $this->delete("/api/posts/{$post->id}");
        $response->assertStatus(204);

        $this->assertDatabaseCount('posts', 1);
        $this->assertDatabaseCount('comments', 3);
    }

    public function test_can_delete_other_user_post_as_moderator(): void
    {
        $this->authUser(Role::MODERATOR);
        $otherUser = User::factory()->role(Role::WRITER)->create();
        $post = Post::factory()->for($otherUser)->create();
        $anotherPost = Post::factory()->for($otherUser)->create();
        Comment::factory()->count(2)->for($otherUser)->for($post)->create();
        Comment::factory()->count(3)->for($otherUser)->for($anotherPost)->create();

        $this->assertDatabaseCount('posts', 2);
        $this->assertDatabaseCount('comments', 5);

        $response = $this->delete("/api/posts/{$post->id}");
        $response->assertStatus(204);

        $this->assertDatabaseCount('posts', 1);
        $this->assertDatabaseCount('comments', 3);
    }

    public function test_cannot_delete_own_post_as_writer(): void
    {
        $user = $this->authUser(Role::WRITER);
        $post = Post::factory()->for($user)->create();
        $anotherPost = Post::factory()->for($user)->create();
        Comment::factory()->count(2)->for($user)->for($post)->create();
        Comment::factory()->count(3)->for($user)->for($anotherPost)->create();

        $this->assertDatabaseCount('posts', 2);
        $this->assertDatabaseCount('comments', 5);

        $response = $this->delete("/api/posts/{$post->id}");
        $response->assertForbidden();

        $this->assertDatabaseCount('posts', 2);
        $this->assertDatabaseCount('comments', 5);
    }

    public function test_cannot_delete_other_user_post_as_writer(): void
    {
        $this->authUser(Role::WRITER);
        $otherUser = User::factory()->role(Role::WRITER)->create();
        $post = Post::factory()->for($otherUser)->create();
        $anotherPost = Post::factory()->for($otherUser)->create();
        Comment::factory()->count(2)->for($otherUser)->for($post)->create();
        Comment::factory()->count(3)->for($otherUser)->for($anotherPost)->create();

        $this->assertDatabaseCount('posts', 2);
        $this->assertDatabaseCount('comments', 5);

        $response = $this->delete("/api/posts/{$post->id}");
        $response->assertForbidden();

        $this->assertDatabaseCount('posts', 2);
        $this->assertDatabaseCount('comments', 5);
    }
    

    public function test_cannot_delete_own_post_as_reader(): void
    {
        // Readers shouldn't be able to create posts, but we test for completeness / role change
        $user = $this->authUser(Role::READER);
        $post = Post::factory()->for($user)->create();
        $anotherPost = Post::factory()->for($user)->create();
        Comment::factory()->count(2)->for($user)->for($post)->create();
        Comment::factory()->count(3)->for($user)->for($anotherPost)->create();

        $this->assertDatabaseCount('posts', 2);
        $this->assertDatabaseCount('comments', 5);

        $response = $this->delete("/api/posts/{$post->id}");
        $response->assertForbidden();

        $this->assertDatabaseCount('posts', 2);
        $this->assertDatabaseCount('comments', 5);
    }

    public function test_cannot_delete_other_user_post_as_reader(): void
    {
        $this->authUser(Role::READER);
        $otherUser = User::factory()->role(Role::WRITER)->create();
        $post = Post::factory()->for($otherUser)->create();
        $anotherPost = Post::factory()->for($otherUser)->create();
        Comment::factory()->count(2)->for($otherUser)->for($post)->create();
        Comment::factory()->count(3)->for($otherUser)->for($anotherPost)->create();

        $this->assertDatabaseCount('posts', 2);
        $this->assertDatabaseCount('comments', 5);

        $response = $this->delete("/api/posts/{$post->id}");
        $response->assertForbidden();

        $this->assertDatabaseCount('posts', 2);
        $this->assertDatabaseCount('comments', 5);
    }
}
