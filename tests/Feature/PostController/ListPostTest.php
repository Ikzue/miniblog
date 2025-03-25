<?php

namespace Tests\Feature\PostController;
use Carbon\Carbon;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use App\Models\Post;
use App\Models\User;
use App\Enums\Role;

class ListPostTest extends TestCase
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
        $response = $this->get("/api/posts");
        $response->assertRedirectToRoute('login');
    }

    public function test_should_return_ok_status(): void
    {
        $this->authUser();
        $response = $this->get("/api/posts");
        $response->assertStatus(200);
    }

    public function test_posts_list_with_correct_formatting(): void
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

    public function test_posts_list_with_descending_dates_order(): void{
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

    public function test_email_only_shown_when_public(): void
    {
        $userPrivateMail = $this->authUser();
        $userPublicMail = User::factory()->isEmailPublic(true)->create();

        $postPrivate = Post::factory()->for($userPrivateMail)->state(['created_at' => '2025-02-01 12:00:00'])->create();
        $postPublic  =  Post::factory()->for($userPublicMail)->state(['created_at' => '2025-02-01 11:00:00'])->create();

        $response = $this->get("/api/posts");
        $response->assertExactJson([
            ['id' => $postPrivate->id,
            'created_at' => $postPrivate->created_at->toDateTimeString(),
            'updated_at' => $postPrivate->updated_at->toDateTimeString(),
            'title' => $postPrivate->title,
            'content' => $postPrivate->content,
            'can_comment' => false,
            'can_delete' => false,
            'can_update' => true,
            'user' => [
                'id' => $postPrivate->user->id,
                'name' => $postPrivate->user->name,
                ]
            ],
            ['id' => $postPublic->id,
            'created_at' => $postPublic->created_at->toDateTimeString(),
            'updated_at' => $postPublic->updated_at->toDateTimeString(),
            'title' => $postPublic->title,
            'content' => $postPublic->content,
            'can_comment' => false,
            'can_delete' => false,
            'can_update' => false,
            'user' => [
                'id' => $postPublic->user->id,
                'name' => $postPublic->user->name,
                'email' => $postPublic->user->email,
                ]
            ],
        ]);
    }
}
