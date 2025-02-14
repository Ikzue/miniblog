<?php

namespace Tests\Feature\PostController;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use App\Models\Post;
use App\Models\User;

class ListPostTest extends TestCase
{
    use RefreshDatabase;

    public function test_no_auth(): void
    {
        $response = $this->get("/api/posts");
        $response->assertRedirectToRoute('login');
    }

    public function test_auth(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);
        $response = $this->get("/api/posts");
    
        $response->assertStatus(200);
    }

    public function test_format(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->for($user)->create();

        $this->actingAs($post->user);
        $response = $this->get("/api/posts");

        $response->assertExactJson([
            ['id' => $post->id,
            'created_at' => $post->created_at->toISOString(),
            'updated_at' => $post->updated_at->toISOString(),
            'title' => $post->title,
            'content' => $post->content,
            'user' => [
                'id' => $post->user->id,
                'name' => $post->user->name
                ]
            ]
        ]);
    }

    public function test_ordering(): void{
        $user = User::factory()->create();
        $createdAtDates = [
            '2025-02-01 12:00:00',
            // id順じゃないことを証明するため、順番をバラバラに
            '2025-02-01 13:00:00',
            '2025-02-01 10:00:00',
            '2025-02-01 09:00:00'
        ];
        Post::factory()
        ->for($user)
        ->count(4)
        ->sequence( fn ($sequence) => ['created_at' => $createdAtDates[$sequence->index]])
        ->create();

        $dates = Post::orderBy('created_at', 'desc')->pluck('created_at')->map->toISOString()->all();

        $this->actingAs($user);
        $response = $this->get("/api/posts");
        $response->assertSeeInOrder($dates);
    }

    public function test_pagination(): void{
        $this->markTestIncomplete('Feature not implemented');
    }

}
