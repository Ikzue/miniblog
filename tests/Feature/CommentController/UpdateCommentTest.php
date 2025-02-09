<?php

namespace Tests\Feature\CommentController;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use App\Models\Comment;

class UpdateCommentTest extends TestCase
{
    use RefreshDatabase;

    public function test_no_auth(): void
    {
        $this->markTestIncomplete("Check PUT 'comments.update' redirect");
    }

    public function test_auth(): void
    {
        $this->markTestIncomplete();
    }

    public function test_update_OK(): void
    {
        $this->markTestIncomplete();
    }

    public function test_update_no_side_effects(): void
    {
        $this->markTestIncomplete("Assert comment has been modified and other comment fields are unchanged");
    }

    public function test_update_missing_field_KO(): void
    {
        $this->markTestIncomplete();
    }

    public function test_patch_disabled(): void
    {
        $this->markTestIncomplete("TODO - Check PATCH 'comment.update' is disabled");
    }
}
