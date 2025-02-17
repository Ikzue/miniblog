<?php

namespace Tests\Feature\PostController;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use App\Models\Post;
use App\Models\User;

class DetailsPostTest extends TestCase
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
        $response = $this->get("/api/posts/{$post->id}");
        $response->assertRedirectToRoute('login');
    }

    public function test_auth(): void
    {
        $user = $this->authUser();
        $post = Post::factory()->for($user)->create();
        $response = $this->get("/api/posts/{$post->id}");
        $response->assertStatus(200);
    }

    public function test_format_OK(): void
    {
        $user = $this->authUser();
        $post = Post::factory()->for($user)->create();

        $response = $this->get("/api/posts/{$post->id}");
        $response->assertExactJson([
            'id' => $post->id,
            'created_at' => $post->created_at->toISOString(),
            'updated_at' => $post->updated_at->toISOString(),
            'title' => $post->title,
            'content' => $post->content,
            'is_own_post' => true
        ]);
    }

    public function test_format_other_user_OK(): void
    {
        $this->authUser();
        $post = Post::factory()->for(User::factory()->create())->create();

        $response = $this->get("/api/posts/{$post->id}");
        $response->assertExactJson([
            'id' => $post->id,
            'created_at' => $post->created_at->toISOString(),
            'updated_at' => $post->updated_at->toISOString(),
            'title' => $post->title,
            'content' => $post->content,
            'is_own_post' => false
        ]);
    }
}
