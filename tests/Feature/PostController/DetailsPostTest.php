<?php

namespace Tests\Feature\PostController;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use App\Models\Post;
use App\Models\User;

class DetailsPostTest extends TestCase
{
    use RefreshDatabase;

    public function test_no_auth(): void
    {
        $this->markTestIncomplete("Check 'posts.show' redirect");
    }

    public function test_auth(): void
    {
        $this->markTestIncomplete();
    }

    public function test_get_OK(): void
    {
        $this->markTestIncomplete("Check format");
    }

    public function test_get_linked_comments_OK(): void
    {
        $this->markTestIncomplete("Check format and ordering");
    }
}
