<?php

namespace Tests\Feature\CommentController;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use App\Models\Comment;

class DeleteCommentTest extends TestCase
{
    use RefreshDatabase;

    public function test_no_auth(): void
    {
        $this->markTestIncomplete("Check DELETE 'comments.destroy' redirect");
    }

    public function test_auth(): void
    {
        $this->markTestIncomplete();
    }

    public function test_delete_OK(): void
    {
        $this->markTestIncomplete("Assert new comment fields - number of comments in db");
    }
}
