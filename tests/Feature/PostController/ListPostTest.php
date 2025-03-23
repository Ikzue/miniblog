<?php

namespace Tests\Feature\PostController;
use Carbon\Carbon;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use App\Models\Post;
use App\Models\User;

class ListPostTest extends TestCase
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
        $response = $this->get("/api/posts");
        $response->assertRedirectToRoute('login');
    }

    public function test_should_return_ok_status(): void
    {
        $this->authUser();
        $response = $this->get("/api/posts");
        $response->assertStatus(200);
    }

    public function test_can_list_posts_with_correct_formatting(): void
    {
        $user = $this->authUser();
        $post = Post::factory()->for($user)->create();

        $response = $this->get("/api/posts");
        $response->assertExactJson([
            ['id' => $post->id,
            'created_at' => $post->created_at->toDateTimeString(),
            'updated_at' => $post->updated_at->toDateTimeString(),
            'title' => $post->title,
            'content' => $post->content,
            'can_comment' => false,
            'can_delete' => false,
            'can_update' => true,
            'user' => [
                'id' => $post->user->id,
                'name' => $post->user->name
                ]
            ]
        ]);
    }

    public function test_can_list_posts_with_descending_dates_order(): void{
        $user = $this->authUser();
        $createdAtDates = [
            '2025-02-01 12:00:00',
            '2025-02-01 13:00:00',
            '2025-02-01 10:00:00',
            '2025-02-01 09:00:00'
        ];
        Post::factory()
            ->count(4)->for($user) 
            ->sequence( fn ($sequence) => ['created_at' => $createdAtDates[$sequence->index]])
            ->create();

        $response = $this->get("/api/posts");
        $response->assertJsonCount(4);

        $response->assertSeeInOrder([
            Carbon::parse('2025-02-01 13:00:00', 'UTC')->toDateTimeString(),
            Carbon::parse('2025-02-01 12:00:00', 'UTC')->toDateTimeString(),
            Carbon::parse('2025-02-01 10:00:00', 'UTC')->toDateTimeString(),
            Carbon::parse('2025-02-01 09:00:00', 'UTC')->toDateTimeString(),
        ]);
    }
}
