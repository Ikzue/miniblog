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

    public function test_no_auth(): void
    {
        $post = Post::factory()->for(User::factory()->create())->create(); 

        $response = $this->post('/api/comments', [
            'content' => 'My content',
            'post_id' => $post->id,
        ]);
        $response->assertRedirectToRoute('login');
    }

    public function test_auth(): void
    {
        $user = $this->authUser();
        $post = Post::factory()->for($user)->create(); 

        $response = $this->post('/api/comments', [
            'content' => 'My content',
            'post_id' => $post->id,
        ]);
        $response->assertStatus(201);
    }

    public function test_create_OK(): void
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

    public function test_create_missing_field_KO(): void
    {
        $this->authUser();
        $this->assertDatabaseCount('comments', 0);

        $response = $this->post('/api/comments', [
            'content' => 'My content',
        ]);
        $response->assertInvalid(['post_id']);

        $this->assertDatabaseCount('comments', 0);
    }

    public function test_get_disabled(): void
    {
        $this->authUser();

        $response = $this->get('/api/comments/create');
        $response->assertStatus(404);
    }
}
