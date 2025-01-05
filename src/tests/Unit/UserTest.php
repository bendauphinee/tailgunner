<?php

namespace Tests\Unit;

use App\Models\Template;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_get_templates()
    {
        $user = User::factory()->create();
        $template = Template::factory()->create(['user_id' => $user->id]);

        $this->assertTrue($user->templates->contains($template));
    }
}