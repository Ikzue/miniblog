<?php

namespace Tests\Feature\PostController;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use App\Models\Post;
use App\Models\User;

class CreatePostTest extends TestCase
{
    use RefreshDatabase;

    public function test_no_auth(): void
    {
        $this->markTestIncomplete("Check POST 'posts.store' redirect");
    }

    public function test_auth(): void
    {
        $this->markTestIncomplete();
    }

    public function test_create_OK(): void
    {
        $this->markTestIncomplete("Assert new post fields and number of posts in db");
    }

    public function test_create_missing_field_KO(): void
    {
        $this->markTestIncomplete();
    }

    public function test_get_disabled(): void
    {
        $this->markTestIncomplete("Check GET 'posts.create' is disabled");
    }
}
