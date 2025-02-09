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
        $response = $this->get(route('posts.index'));
        $response->assertRedirectToRoute('login');
    }

    public function test_auth(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);
        $response = $this->get(route('posts.index'));
    
        $response->assertStatus(200);
    }

    public function test_format(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->withUser($user)->create();

        $this->actingAs($user);
        $response = $this->get(route('posts.index'));

        $response->assertExactJson([
            ['id' => $post->id,
            'created_at' => $post->created_at->toISOString(),
            'updated_at' => $post->updated_at->toISOString(),
            'title' => $post->title,
            'content' => $post->content,
            'user' => [
                'id' => $user->id,
                'name' => $user->name
                ]
            ]
        ]);
    }

    public function test_ordering(): void{
        $user = User::factory()->create();
        Post::factory()
        ->withUser($user)
        ->count(4)
        ->create();

        $posts = Post::orderBy('created_at', 'desc')->get();
        $dates = $posts->pluck('created_at')->all();
        $dates = array_map(
            function($date){ return $date->toISOString();},
            $dates
        );

        $this->actingAs($user);
        $response = $this->get(route('posts.index'));
        $response->assertSeeInOrder($dates);
    }

    public function test_pagination(): void{
        $this->markTestIncomplete('Feature not implemented');
    }

}
