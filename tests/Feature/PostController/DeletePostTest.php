<?php

namespace Tests\Feature\PostController;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use App\Models\Post;
use App\Models\User;

class DeletePostTest extends TestCase
{
    use RefreshDatabase;

    public function test_no_auth(): void
    {
        $this->markTestIncomplete("Check DELETE 'posts.destroy' redirect");
    }

    public function test_auth(): void
    {
        $this->markTestIncomplete();
    }

    public function test_delete_OK(): void
    {
        $this->markTestIncomplete("Assert fields new post and number of posts in db");
    }

    public function test_delete_linked_comments_OK(): void
    {
        $this->markTestIncomplete();
    }
}
