<?php

namespace Tests\Feature\CommentController;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;

class UpdateCommentTest extends TestCase
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

        $response = $this->put("/api/comments/{$comment->id}", [
            'content' => 'New content'
        ]);
        $response->assertRedirectToRoute('login');
    }

    public function test_should_return_ok_status(): void
    {
        $user = $this->authUser();
        $post = Post::factory()->for($user)->create();
        $comment = Comment::factory()->for($user)->for($post)->create(["content" => "Old content"]);

        $response = $this->put("/api/comments/{$comment->id}", [
            'content' => 'New content',
        ]);
        $response->assertOk();
    }

    public function test_can_edit_comment(): void
    {
        $user = $this->authUser();
        $post = Post::factory()->for($user)->create();
        $comment = Comment::factory()->for($user)->for($post)->create(["content" => "Old content"]);

        $response = $this->put("/api/comments/{$comment->id}", [
            'content' => 'New content',
        ]);
        $response->assertOk();

        $this->assertDatabaseCount('comments', 1);
        $this->assertDatabaseHas('comments', [
            'content' => 'New content',
            'post_id' => $post->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_can_edit_comment_without_side_effects(): void
    {
        $user = $this->authUser();
        $post = Post::factory()->for($user)->create();
        $comment = Comment::factory()->for($user)->for($post)->create(["content" => "Comment 1"]);
        $otherComment = Comment::factory()->for($user)->for($post)->create(["content" => "Comment 2"]);

        $response = $this->put("/api/comments/{$comment->id}", [
            'content' => 'New content',
        ]);
        $response->assertOk();

        $this->assertDatabaseCount('comments', 2);
        $this->assertDatabaseHas('comments', [
            'content' => 'New content',
            'post_id' => $post->id,
            'user_id' => $user->id,
        ]);
        $this->assertDatabaseHas('comments', [
            'content' => 'Comment 2',
            'post_id' => $post->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_cannot_update_with_missing_field(): void
    {
        $user = $this->authUser();
        $post = Post::factory()->for($user)->create();
        $comment = Comment::factory()->for($user)->for($post)->create(["content" => "Old content"]);

        $response = $this->put("/api/comments/{$comment->id}", []);
        $response->assertInvalid(['content' => 'The content field is required.']);

        $this->assertDatabaseCount('comments', 1);
        $this->assertDatabaseHas('comments', [
            'content' => 'Old content',
            'post_id' => $post->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_cannot_update_other_user_comment(): void
    {
        $this->authUser();
        $otherUser = User::factory()->create();
        $post = Post::factory()->for($otherUser)->create();
        $comment = Comment::factory()->for($otherUser)->for($post)->create(["content" => "Old content"]);

        $response = $this->put("/api/comments/{$comment->id}", [
            'content' => 'New content',
        ]);
        $response->assertStatus(403);

        $this->assertDatabaseCount('comments', 1);
        $this->assertDatabaseHas('comments', [
            'content' => 'Old content',
            'post_id' => $post->id,
            'user_id' => $otherUser->id,
        ]);
    }
}
