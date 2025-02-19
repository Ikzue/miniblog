<?php

namespace Tests\Feature\CommentController;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;

class DeleteCommentTest extends TestCase
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
        $comment = Comment::factory()->for($user)->for($post)->create();

        $response = $this->delete("/api/comments/{$comment->id}");
        $response->assertRedirectToRoute('login');

        $this->assertDatabaseCount('comments', 1);
    }

    public function test_can_delete_comment_without_side_effects(): void
    {
        $user = $this->authUser();
        $post = Post::factory()->for($user)->create();
        $comment = Comment::factory()->for($user)->for($post)->create();

        $response = $this->delete("/api/comments/{$comment->id}");
        $response->assertStatus(204);
        $this->assertDatabaseCount('comments', 0);
    }

    public function test_cannot_delete_other_user_comments(): void
    {
        $this->authUser();
        $otherUser = User::factory()->create();
        $post = Post::factory()->for($otherUser)->create();
        $comment = Comment::factory()->for($otherUser)->for($post)->create();

        $response = $this->delete("/api/comments/{$comment->id}");
        $response->assertStatus(403);
        $this->assertDatabaseCount('comments', 1);
    }
}
