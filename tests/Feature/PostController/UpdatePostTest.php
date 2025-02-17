<?php

namespace Tests\Feature\PostController;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use App\Models\Post;
use App\Models\User;

class UpdatePostTest extends TestCase
{
    use RefreshDatabase;

    private function authUser() {
        $user = User::factory()->create();
        $this->actingAs($user);
        return $user;
    }
    
    public function test_no_auth(): void {
        $user = User::factory()->create();
        $post = Post::factory()->for($user)->create();

        $response = $this->put("/api/posts/{$post->id}", [
            'title' => 'New title',
            'content' => 'New content'
        ]);
        $response->assertRedirectToRoute('login');
    }
    
    public function test_auth(): void {
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
    
    public function test_update_OK(): void {
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

        $this->assertDatabaseCount('posts', 1);
        $this->assertDatabaseHas('posts', [
            'title' => 'New title',
            'content' => 'New content',
            'user_id' => $user->id,
        ]);
    }
    
    public function test_update_no_side_effects_OK(): void {
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
    
    public function test_update_missing_field_KO(): void {
        $user = $this->authUser();
        $post = Post::factory()->for($user)->create([
            'title' => 'Old title',
            'content' => 'Old content'
        ]);

        $response = $this->put("/api/posts/{$post->id}", [
            'content' => 'New content'
        ]);
        $response->assertInvalid(['title']);

        $this->assertDatabaseCount('posts', 1);
        $this->assertDatabaseHas('posts', [
            'title' => 'Old title',
            'content' => 'Old content',
            'user_id' => $user->id,
        ]);
    }
    
    public function test_update_other_user_KO(): void {
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
        $response->assertStatus(403);

        $this->assertDatabaseCount('posts', 1);
        $this->assertDatabaseHas('posts', [
            'title' => 'Old title',
            'content' => 'Old content',
            'user_id' => $otherUser->id,
        ]);
    }
    
    public function test_patch_disabled(): void {
        $user = $this->authUser();
        $post = Post::factory()->for($user)->create([
            'title' => 'Old title',
            'content' => 'Old content'
        ]);

        $response = $this->patch("/api/posts/{$post->id}", [
            'content' => 'New content',
        ]);
        $response->assertStatus(405);

        $this->assertDatabaseHas('posts', [
            'title' => 'Old title',
            'content' => 'Old content',
            'user_id' => $user->id,
        ]);
    }
}
