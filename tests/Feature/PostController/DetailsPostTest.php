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

    public function test_should_redirect_guest(): void
    {
        $post = Post::factory()->for(User::factory()->create())->create();
        $response = $this->get("/api/posts/{$post->id}");
        $response->assertRedirectToRoute('login');
    }

    public function test_should_return_ok_status(): void
    {
        $user = $this->authUser();
        $post = Post::factory()->for($user)->create();
        $response = $this->get("/api/posts/{$post->id}");
        $response->assertStatus(200);
    }

    public function test_can_get_post_details_with_correct_formatting(): void
    {
        $user = $this->authUser();
        $post = Post::factory()->for($user)->create();

        $response = $this->get("/api/posts/{$post->id}");
        $response->assertExactJson([
            'id' => $post->id,
            'created_at' => $post->created_at->toDateTimeString(),
            'updated_at' => $post->updated_at->toDateTimeString(),
            'title' => $post->title,
            'content' => $post->content,
            'can_comment' => false,
            'can_delete' => false,
            'can_update' => true,
        ]);
    }

    public function test_can_get_other_user_post_details_with_correct_formatting(): void
    {
        $this->authUser();
        $post = Post::factory()->for(User::factory()->create())->create();

        $response = $this->get("/api/posts/{$post->id}");
        $response->assertExactJson([
            'id' => $post->id,
            'created_at' => $post->created_at->toDateTimeString(),
            'updated_at' => $post->updated_at->toDateTimeString(),
            'title' => $post->title,
            'content' => $post->content,
            'can_comment' => false,
            'can_delete' => false,
            'can_update' => false,
        ]);
    }
}
