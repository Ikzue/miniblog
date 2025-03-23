<?php

namespace Tests\Feature\CommentController;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use App\Enums\Role;

class UpdateCommentTest extends TestCase
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

    public function test_can_edit_comment_without_side_effects(): void
    {
        $user = $this->authUser(Role::MODERATOR);
        $post = Post::factory()->for($user)->create();
        $comment = Comment::factory()->for($user)->for($post)->create(["content" => "Comment 1"]);
        Comment::factory()->for($user)->for($post)->create(["content" => "Comment 2"]);

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

    public function test_can_edit_other_user_comment_as_moderator(): void
    {
        $this->authUser(Role::MODERATOR);
        $otherUser = User::factory()->role(Role::WRITER)->create();
        $post = Post::factory()->for($otherUser)->create();
        $comment = Comment::factory()->for($otherUser)->for($post)->create(["content" => "Old content"]);

        $response = $this->put("/api/comments/{$comment->id}", [
            'content' => 'New content',
        ]);
        $response->assertOk();

        $this->assertDatabaseCount('comments', 1);
        $this->assertDatabaseHas('comments', [
            'content' => 'New content',
            'post_id' => $post->id,
            'user_id' => $otherUser->id,
        ]);
    }

    public function test_can_edit_own_comment_as_writer(): void
    {
        $user = $this->authUser(Role::WRITER);
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

    public function test_cannot_edit_other_user_comment_as_writer(): void
    {
        $this->authUser(Role::WRITER);
        $otherUser = User::factory()->create();
        $post = Post::factory()->for($otherUser)->create();
        $comment = Comment::factory()->for($otherUser)->for($post)->create(["content" => "Old content"]);

        $response = $this->put("/api/comments/{$comment->id}", [
            'content' => 'New content',
        ]);
        $response->assertForbidden();

        $this->assertDatabaseCount('comments', 1);
        $this->assertDatabaseHas('comments', [
            'content' => 'Old content',
            'post_id' => $post->id,
            'user_id' => $otherUser->id,
        ]);
    }

    public function test_can_edit_own_comment_as_reader(): void
    {
        $user = $this->authUser(Role::READER);
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

    public function test_cannot_update_other_user_comment_as_reader(): void
    {
        $this->authUser(Role::READER);
        $otherUser = User::factory()->create();
        $post = Post::factory()->for($otherUser)->create();
        $comment = Comment::factory()->for($otherUser)->for($post)->create(["content" => "Old content"]);

        $response = $this->put("/api/comments/{$comment->id}", [
            'content' => 'New content',
        ]);
        $response->assertForbidden();

        $this->assertDatabaseCount('comments', 1);
        $this->assertDatabaseHas('comments', [
            'content' => 'Old content',
            'post_id' => $post->id,
            'user_id' => $otherUser->id,
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
}
