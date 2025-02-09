<?php

namespace Tests\Feature\PostController;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use App\Models\Post;
use App\Models\User;

class UpdatePostTest extends TestCase
{
    use RefreshDatabase;

    public function test_no_auth(): void
    {
        $this->markTestIncomplete("Check PUT 'posts.update' redirect");
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
        $this->markTestIncomplete("Assert post has been modified and other post fields are unchanged");
    }

    public function test_update_missing_field_KO(): void
    {
        $this->markTestIncomplete();
    }

    public function test_patch_disabled(): void
    {
        $this->markTestIncomplete("TODO - Check PATCH 'posts.update' is disabled");
    }
}
