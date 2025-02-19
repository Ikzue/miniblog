<?php

namespace Tests\Feature\CommentController;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use App\Models\Comment;
use App\Models\User;
use App\Models\Post;

class CreateCommentTest extends TestCase
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

    public function test_can_create_post_and_check_side_effects(): void
    {
        $user = $this->authUser();
        $post = Post::factory()->for($user)->create(); 
        $reqData = [
            'content' => 'My content',
            'post_id' => $post->id,
        ];
        $this->assertDatabaseCount('comments', 0);

        $response = $this->post('/api/comments', $reqData);
        $response->assertCreated();

        $this->assertDatabaseCount('comments', 1);
        $this->assertDatabaseHas('comments', [
            'content' => $reqData['content'],
            'post_id' => $post->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_cannot_create_post_when_missing_field(): void
    {
        $this->authUser();
        $this->assertDatabaseCount('comments', 0);

        $response = $this->post('/api/comments', [
            'content' => 'My content',
        ]);
        $response->assertInvalid(['post_id']);

        $this->assertDatabaseCount('comments', 0);
    }
}
