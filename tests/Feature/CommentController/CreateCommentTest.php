<?php

namespace Tests\Feature\CommentController;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use App\Models\Comment;
use App\Models\User;
use App\Models\Post;
use App\Models\Enums\Role;

class CreateCommentTest extends TestCase
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
        $post = Post::factory()->for(User::factory()->create())->create(); 

        $response = $this->post('/api/comments', [
            'content' => 'My content',
            'post_id' => $post->id,
        ]);
        $response->assertRedirectToRoute('login');
    }

    public function test_should_return_ok_status(): void
    {
        $user = $this->authUser();
        $post = Post::factory()->for($user)->create(); 

        $response = $this->post('/api/comments', [
            'content' => 'My content',
            'post_id' => $post->id,
        ]);
        $response->assertStatus(201);
    }

    public function test_can_comment_and_check_side_effects(): void
    {
        $user = $this->authUser(Role::MODERATOR);
        $post = Post::factory()->for($user)->create(); 

        $this->assertDatabaseCount('comments', 0);
        $response = $this->post('/api/comments', [
            'content' => 'My content',
            'post_id' => $post->id,
        ]);
        $response->assertCreated();
        $this->assertDatabaseCount('comments', 1);
        $this->assertDatabaseHas('comments', [
            'content' => 'My content',
            'post_id' => $post->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_can_comment_on_other_user_post_as_moderator(): void
    {
        $user = $this->authUser(Role::MODERATOR);
        $otherUser = User::factory()->role(Role::WRITER)->create();
        $post = Post::factory()->for($otherUser)->create(); 

        $this->assertDatabaseCount('comments', 0);
        $response = $this->post('/api/comments', [
            'content' => 'My content',
            'post_id' => $post->id,
        ]);
        $response->assertCreated();
        $this->assertDatabaseCount('comments', 1);
        $this->assertDatabaseHas('comments', [
            'content' => 'My content',
            'post_id' => $post->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_can_comment_on_own_post_as_writer(): void
    {
        $user = $this->authUser(Role::WRITER);
        $post = Post::factory()->for($user)->create(); 

        $this->assertDatabaseCount('comments', 0);
        $response = $this->post('/api/comments', [
            'content' => 'My content',
            'post_id' => $post->id,
        ]);
        $response->assertCreated();
        $this->assertDatabaseCount('comments', 1);
        $this->assertDatabaseHas('comments', [
            'content' => 'My content',
            'post_id' => $post->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_cannot_comment_on_other_user_post_as_writer(): void
    {
        $user = $this->authUser(Role::WRITER);
        $otherUser = User::factory()->role(Role::WRITER)->create();
        $post = Post::factory()->for($otherUser)->create(); 

        $this->assertDatabaseCount('comments', 0);
        $response = $this->post('/api/comments', [
            'content' => 'My content',
            'post_id' => $post->id,
        ]);
        $response->assertForbidden();
        $this->assertDatabaseCount('comments', 0);
    }

    public function test_can_comment_on_own_post_as_reader(): void
    {
        $user = $this->authUser(Role::READER);
        $post = Post::factory()->for($user)->create(); 

        $this->assertDatabaseCount('comments', 0);
        $response = $this->post('/api/comments', [
            'content' => 'My content',
            'post_id' => $post->id,
        ]);
        $response->assertCreated();
        $this->assertDatabaseCount('comments', 1);
        $this->assertDatabaseHas('comments', [
            'content' => 'My content',
            'post_id' => $post->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_can_comment_on_other_user_post_as_reader(): void
    {
        $user = $this->authUser(Role::READER);
        $otherUser = User::factory()->role(Role::WRITER)->create();
        $post = Post::factory()->for($otherUser)->create(); 

        $this->assertDatabaseCount('comments', 0);
        $response = $this->post('/api/comments', [
            'content' => 'My content',
            'post_id' => $post->id,
        ]);
        $response->assertCreated();
        $this->assertDatabaseCount('comments', 1);
        $this->assertDatabaseHas('comments', [
            'content' => 'My content',
            'post_id' => $post->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_cannot_create_comment_when_missing_field(): void
    {
        $this->authUser();
        $this->assertDatabaseCount('comments', 0);

        $response = $this->post('/api/comments', [
            'content' => 'My content',
        ]);
        $response->assertInvalid(['post_id' => 'The post id field is required.']);

        $this->assertDatabaseCount('comments', 0);
    }
}
