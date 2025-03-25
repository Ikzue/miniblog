<?php

namespace Tests\Feature\CommentController;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use App\Models\Enums\Role;

class DeleteCommentTest extends TestCase
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
        $comment = Comment::factory()->for($user)->for($post)->create();

        $response = $this->delete("/api/comments/{$comment->id}");
        $response->assertRedirectToRoute('login');

        $this->assertDatabaseCount('comments', 1);
    }

    public function test_can_delete_comment_without_side_effects(): void
    {
        $user = $this->authUser(Role::MODERATOR);
        $post = Post::factory()->for($user)->create();
        $anotherPost = Post::factory()->for($user)->create();
        $comment = Comment::factory()->for($user)->for($post)->create();
        Comment::factory()->count(2)->for($user)->for($post)->create();
        Comment::factory()->count(2)->for($user)->for($anotherPost)->create();

        $this->assertDatabaseCount('comments', 5);
        $response = $this->delete("/api/comments/{$comment->id}");
        $response->assertStatus(204);
        $this->assertDatabaseCount('comments', 4);
    }

    public function test_can_delete_other_user_comments_as_moderator(): void
    {
        $this->authUser(Role::MODERATOR);
        $otherUser = User::factory()->create();
        $post = Post::factory()->for($otherUser)->create();
        $comment = Comment::factory()->for($otherUser)->for($post)->create();

        $response = $this->delete("/api/comments/{$comment->id}");
        $response->assertStatus(204);
        $this->assertDatabaseCount('comments', 0);
    }
    
    public function test_can_delete_own_comments_as_writer(): void
    {
        $user = $this->authUser(Role::WRITER);
        $post = Post::factory()->for($user)->create();
        $comment = Comment::factory()->for($user)->for($post)->create();

        $response = $this->delete("/api/comments/{$comment->id}");
        $response->assertStatus(204);
        $this->assertDatabaseCount('comments', 0);
    }

    public function test_cannot_delete_other_user_comments_as_writer(): void
    {
        $this->authUser(Role::WRITER);
        $otherUser = User::factory()->create();
        $post = Post::factory()->for($otherUser)->create();
        $comment = Comment::factory()->for($otherUser)->for($post)->create();

        $response = $this->delete("/api/comments/{$comment->id}");
        $response->assertForbidden();
        $this->assertDatabaseCount('comments', 1);
    }

    public function test_can_delete_own_comments_as_reader(): void
    {
        $user = $this->authUser(Role::READER);
        $post = Post::factory()->for($user)->create();
        $comment = Comment::factory()->for($user)->for($post)->create();

        $response = $this->delete("/api/comments/{$comment->id}");
        $response->assertStatus(204);
        $this->assertDatabaseCount('comments', 0);
    }

    public function test_cannot_delete_other_user_comments_as_reader(): void
    {
        $this->authUser(Role::READER);
        $otherUser = User::factory()->create();
        $post = Post::factory()->for($otherUser)->create();
        $comment = Comment::factory()->for($otherUser)->for($post)->create();

        $response = $this->delete("/api/comments/{$comment->id}");
        $response->assertForbidden();
        $this->assertDatabaseCount('comments', 1);
    }
}
