<?php

namespace Tests\Feature\CommentController;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use App\Models\Comment;
use App\Models\User;

class ListCommentTest extends TestCase
{
    use RefreshDatabase;

    public function test_no_auth(): void
    {
        $this->markTestIncomplete("Check 'comments.index' redirect");
    }

    public function test_auth(): void
    {
        $this->markTestIncomplete();
    }

    public function test_format(): void
    {
        $this->markTestIncomplete();
    }

    public function test_ordering(): void{
        $this->markTestIncomplete();
    }

    public function test_pagination(): void{
        $this->markTestIncomplete('Feature not implemented');
    }
}
