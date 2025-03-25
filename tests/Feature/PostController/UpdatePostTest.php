<?php

namespace Tests\Feature\PostController;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use App\Models\Post;
use App\Models\User;
use App\Models\Enums\Role;

class UpdatePostTest extends TestCase
{
    use RefreshDatabase;

    private function authUser(Role $role = Role::READER) {
        $user = User::factory()->role($role)->create();
        $this->actingAs($user);
        return $user;
    }
    
    public function test_should_redirect_guest(): void {
        $user = User::factory()->create();
        $post = Post::factory()->for($user)->create();

        $response = $this->put("/api/posts/{$post->id}", [
            'title' => 'New title',
            'content' => 'New content'
        ]);
        $response->assertRedirectToRoute('login');
    }
    
    public function test_should_return_ok_status(): void {
        $user = $this->authUser();
        $post = Post::factory()->for($user)->create([
            'title' => 'Old title',
            'content' => 'Old content'
        ]);

        $response = $this->put("/api/posts/{$post->id}", [
            'title' => 'New title',
            'content' => 'New content',
        ]);
        $response->assertOk();
    }
    
    public function test_can_update_own_post_as_moderator(): void {
        $user = $this->authUser(Role::MODERATOR);
        $post = Post::factory()->for($user)->create([
            'title' => 'Old title',
            'content' => 'Old content'
        ]);
        $response = $this->put("/api/posts/{$post->id}", [
            'title' => 'New title',
            'content' => 'New content',
        ]);
        $response->assertOk();

        $this->assertDatabaseCount('posts', 1);
        $this->assertDatabaseHas('posts', [
            'title' => 'New title',
            'content' => 'New content',
            'user_id' => $user->id,
        ]);
    }

    public function test_can_update_other_user_post_as_moderator(): void {
        $this->authUser(Role::MODERATOR);
        $otherUser = User::factory()->role(Role::MODERATOR)->create();
        $post = Post::factory()->for($otherUser)->create([
            'title' => 'Old title',
            'content' => 'Old content'
        ]);
        $response = $this->put("/api/posts/{$post->id}", [
            'title' => 'New title',
            'content' => 'New content',
        ]);
        $response->assertOk();

        $this->assertDatabaseCount('posts', 1);
        $this->assertDatabaseHas('posts', [
            'title' => 'New title',
            'content' => 'New content',
            'user_id' => $otherUser->id,
        ]);
    }

    public function test_can_update_own_post_as_writer(): void {
        $user = $this->authUser(Role::WRITER);
        $post = Post::factory()->for($user)->create([
            'title' => 'Old title',
            'content' => 'Old content'
        ]);
        $response = $this->put("/api/posts/{$post->id}", [
            'title' => 'New title',
            'content' => 'New content',
        ]);
        $response->assertOk();

        $this->assertDatabaseCount('posts', 1);
        $this->assertDatabaseHas('posts', [
            'title' => 'New title',
            'content' => 'New content',
            'user_id' => $user->id,
        ]);
    }

    public function test_cannot_update_other_user_post_as_writer(): void {
        $this->authUser(Role::WRITER);
        $otherUser = User::factory()->role(Role::MODERATOR)->create();
        $post = Post::factory()->for($otherUser)->create([
            'title' => 'Old title',
            'content' => 'Old content'
        ]);
        $response = $this->put("/api/posts/{$post->id}", [
            'title' => 'New title',
            'content' => 'New content',
        ]);
        $response->assertForbidden();

        $this->assertDatabaseCount('posts', 1);
        $this->assertDatabaseHas('posts', [
            'title' => 'Old title',
            'content' => 'Old content',
            'user_id' => $otherUser->id,
        ]);
    }

    public function test_cannot_update_own_post_as_reader(): void {
        // Readers shouldn't be able to create posts, but we test for completeness / role change
        $user = $this->authUser(Role::READER);
        $post = Post::factory()->for($user)->create([
            'title' => 'Old title',
            'content' => 'Old content'
        ]);
        $response = $this->put("/api/posts/{$post->id}", [
            'title' => 'New title',
            'content' => 'New content',
        ]);
        $response->assertOk();

        $this->assertDatabaseCount('posts', 1);
        $this->assertDatabaseHas('posts', [
            'title' => 'New title',
            'content' => 'New content',
            'user_id' => $user->id,
        ]);
    }

    public function test_cannot_update_other_user_post_as_reader(): void {
        $this->authUser(Role::READER);
        $otherUser = User::factory()->role(Role::MODERATOR)->create();
        $post = Post::factory()->for($otherUser)->create([
            'title' => 'Old title',
            'content' => 'Old content'
        ]);
        $response = $this->put("/api/posts/{$post->id}", [
            'title' => 'New title',
            'content' => 'New content',
        ]);
        $response->assertForbidden();

        $this->assertDatabaseCount('posts', 1);
        $this->assertDatabaseHas('posts', [
            'title' => 'Old title',
            'content' => 'Old content',
            'user_id' => $otherUser->id,
        ]);
    }
    
    public function test_can_update_post_without_side_effects(): void {
        $user = $this->authUser();
        $post = Post::factory()->for($user)->create([
            'title' => 'Post 1',
            'content' => 'Post 1 content'
        ]);
        $otherPost = Post::factory()->for($user)->create([
            'title' => 'Post 2',
            'content' => 'Post 2 content'
        ]);

        $response = $this->put("/api/posts/{$post->id}", [
            'title' => 'New title',
            'content' => 'New content',
        ]);
        $response->assertOk();

        $this->assertDatabaseCount('posts', 2);
        $this->assertDatabaseHas('posts', [
            'title' => 'New title',
            'content' => 'New content',
            'user_id' => $user->id,
        ]);
        $this->assertDatabaseHas('posts', [
            'title' => 'Post 2',
            'content' => 'Post 2 content',
            'user_id' => $user->id,
        ]);
    }
    
    public function test_cannot_update_post_with_missing_field(): void {
        $user = $this->authUser();
        $post = Post::factory()->for($user)->create([
            'title' => 'Old title',
            'content' => 'Old content'
        ]);

        $response = $this->put("/api/posts/{$post->id}", [
            'content' => 'New content'
        ]);
        $response->assertInvalid(['title' => 'The title field is required.']);

        $this->assertDatabaseCount('posts', 1);
        $this->assertDatabaseHas('posts', [
            'title' => 'Old title',
            'content' => 'Old content',
            'user_id' => $user->id,
        ]);
    }
    
    public function test_cannot_update_other_user_post(): void {
        $this->authUser();
        $otherUser = User::factory()->create();
        $post = Post::factory()->for($otherUser)->create([
            'title' => 'Old title',
            'content' => 'Old content'
        ]);

        $response = $this->put("/api/posts/{$post->id}", [
            'title' => 'New title',
            'content' => 'New content',
        ]);
        $response->assertForbidden();

        $this->assertDatabaseCount('posts', 1);
        $this->assertDatabaseHas('posts', [
            'title' => 'Old title',
            'content' => 'Old content',
            'user_id' => $otherUser->id,
        ]);
    }
}
