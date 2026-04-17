<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserRoleColumnTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_role_column_can_be_updated_to_jemaat_and_checked_by_has_role(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
        ]);

        $user->update([
            'role' => 'jemaat',
        ]);

        $user = $user->fresh();

        $this->assertSame('jemaat', $user->role);
        $this->assertTrue($user->hasRole('jemaat'));
        $this->assertTrue($user->hasRole(['admin', 'jemaat']));
        $this->assertFalse($user->hasRole('admin'));
    }
}
