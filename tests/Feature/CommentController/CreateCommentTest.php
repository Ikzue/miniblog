<?php

namespace Tests\Feature\CommentController;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use App\Models\Comment;

class CreateCommentTest extends TestCase
{
    use RefreshDatabase;

    public function test_no_auth(): void
    {
        $this->markTestIncomplete("Check POST 'comments.store' redirect");
    }

    public function test_auth(): void
    {
        $this->markTestIncomplete();
    }

    public function test_create_OK(): void
    {
        $this->markTestIncomplete("Assert new comment fields and number of comments in db");
    }

    public function test_create_missing_field_KO(): void
    {
        $this->markTestIncomplete();
    }

    public function test_get_disabled(): void
    {
        $this->markTestIncomplete("Check GET 'comments.create' is disabled");
    }
}
