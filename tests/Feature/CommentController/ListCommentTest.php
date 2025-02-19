<?php

namespace Tests\Feature\CommentController;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

use Tests\TestCase;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;

class ListCommentTest extends TestCase
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
        $response = $this->get("/api/comments");
        $response->assertRedirectToRoute('login');
    }

    public function test_should_return_ok_status(): void
    {
        $this->authUser();

        $response = $this->get("/api/comments");
        $response->assertStatus(200);
    }

    public function test_should_list_post_comments_with_correct_formatting(): void
    {
        $user = $this->authUser();
        $post = Post::factory()->for($user)->create();
        $comment = Comment::factory()->for($user)->for($post)->create();

        $response = $this->get("/api/comments");
        $response->assertExactJson([[
            'id' => $comment->id,
            'created_at' => $comment->created_at->toISOString(),
            'updated_at' => $comment->updated_at->toISOString(),
            'content' => $comment->content,
            'user' => [
                'id' => $comment->user->id,
                'name' => $comment->user->name
            ],
            'post' => [
                'id' => $comment->post->id,
                'title' => $comment->post->title
            ]
        ]]);
    }

    /** List comments for a single post page */
    public function test_should_list_post_comments_with_descending_dates_order(): void{
        $user = $this->authUser();
        $post = Post::factory()->for($user)->create();
        $someOtherPost = Post::factory()->for($user)->create();
        $createdAtDates = [
            '2025-02-01 12:00:00',
            '2025-02-01 13:00:00',
            '2025-02-01 10:00:00',
            '2025-02-03 14:00:00',
            '2025-01-05 09:00:00'
        ];

        // Create post's comments
        // User 1: 2 comments
        Comment::factory()->count(2)
        ->for($user)->for($post)->sequence(
            ['created_at' => $createdAtDates[0]], 
            ['created_at' => $createdAtDates[1]]
        )->create();
        // User 2: 3 comments
        Comment::factory()->count(3)
        ->for(User::factory()->create())->for($post)->sequence(
            ['created_at' => $createdAtDates[2]], 
            ['created_at' => $createdAtDates[3]], 
            ['created_at' => $createdAtDates[4]], 
        )->create();

        // Other posts that shouldn't be shown
        Comment::factory()->count(1)
            ->for($user)->for($someOtherPost)->create();
        Comment::factory()->count(2)
            ->for(User::factory()->create())->for($someOtherPost)->create();

        // Check number of posts and order
        $response = $this->get("/api/comments?post_id={$post->id}");
        $response->assertJsonCount(5);
        $response->assertSeeInOrder([
            Carbon::parse('2025-02-03 14:00:00', 'UTC')->toISOString(),
            Carbon::parse('2025-02-01 13:00:00', 'UTC')->toISOString(),
            Carbon::parse('2025-02-01 12:00:00', 'UTC')->toISOString(),
            Carbon::parse('2025-02-01 10:00:00', 'UTC')->toISOString(),
            Carbon::parse('2025-01-05 09:00:00', 'UTC')->toISOString(),
        ]);
    }

    /** List comments for a user 'my comments' page */
    public function test_should_list_my_comments_with_descending_dates_order(): void{
        $user = $this->authUser();
        $posts = Post::factory()
            ->count(4)
            ->sequence(fn () => ['user_id' => User::factory()->create()])
            ->create();

        $createdAtDates = [
            '2025-02-01 12:00:00',
            '2025-02-01 13:00:00',
            '2025-02-01 10:00:00',
            '2025-02-01 09:00:00'
        ];

        // User comments
        Comment::factory()
            ->count(4)
            ->for($user)
            ->sequence(fn($sequence) => [
                'post_id' =>$posts[$sequence->index]->id,
                'created_at' => $createdAtDates[$sequence->index]
            ])
            ->create();

        // Other comments
        Comment::factory()
            ->count(4)
            ->sequence(fn($sequence) => [
                'user_id' => User::factory()->create(),
                'post_id' => $posts[$sequence->index]->id,
                'created_at' => fake()->date()
            ])
            ->create();

        // Check number of comments
        $response = $this->get("/api/comments?my_comments=true");
        $response->assertJsonCount(4);

        // Check order
        $response->assertSeeInOrder([
            Carbon::parse('2025-02-01 13:00:00', 'UTC')->toISOString(),
            Carbon::parse('2025-02-01 12:00:00', 'UTC')->toISOString(),
            Carbon::parse('2025-02-01 10:00:00', 'UTC')->toISOString(),
            Carbon::parse('2025-02-01 09:00:00', 'UTC')->toISOString(),
        ]);
    }
}
