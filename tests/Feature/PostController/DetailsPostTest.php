<?php

namespace Tests\Feature\PostController;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use App\Models\Post;
use App\Models\User;
use App\Models\Enums\Role;

class DetailsPostTest extends TestCase
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
            'can_comment' => true,
            'can_delete' => false,
            'can_update' => true,
            'user' => [
                'id' => $post->user->id,
                'display' => $post->user->name,
            ],
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
            'can_comment' => true,
            'can_delete' => false,
            'can_update' => false,
            'user' => [
                'id' => $post->user->id,
                'display' => $post->user->name,
            ],
        ]);
    }

    public function test_email_shown_when_public(): void {
        $this->authUser();
        $userPublicMail = User::factory()->isEmailPublic(true)->create();
        $post = Post::factory()->for($userPublicMail)->create();

        $response = $this->get("/api/posts/{$post->id}");
        $response->assertExactJson([
            'id' => $post->id,
            'created_at' => $post->created_at->toDateTimeString(),
            'updated_at' => $post->updated_at->toDateTimeString(),
            'title' => $post->title,
            'content' => $post->content,
            'can_comment' => true,
            'can_delete' => false,
            'can_update' => false,
            'user' => [
                'id' => $post->user->id,
                'display' => "{$post->user->name} <{$post->user->email}>",
            ],
        ]);
    }

    public function test_email_hidden_when_private(): void {
        $this->authUser();
        $userPrivateMail = User::factory()->isEmailPublic(false)->create();
        $post = Post::factory()->for($userPrivateMail)->create();

        $response = $this->get("/api/posts/{$post->id}");
        $response->assertExactJson([
            'id' => $post->id,
            'created_at' => $post->created_at->toDateTimeString(),
            'updated_at' => $post->updated_at->toDateTimeString(),
            'title' => $post->title,
            'content' => $post->content,
            'can_comment' => true,
            'can_delete' => false,
            'can_update' => false,
            'user' => [
                'id' => $post->user->id,
                'display' => $post->user->name,
            ],
        ]);
    }
}
