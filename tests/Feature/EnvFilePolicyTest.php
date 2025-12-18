<?php

namespace Tests\Feature;

use App\Models\EnvFile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EnvFilePolicyTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that authenticated users can view any (their own) env files
     */
    public function test_user_can_view_any_env_files(): void
    {
        $user = User::factory()->create();
        
        $this->assertTrue($user->can('viewAny', EnvFile::class));
    }

    /**
     * Test that users can view their own env files
     */
    public function test_user_can_view_own_env_file(): void
    {
        $user = User::factory()->create();
        
        $envFile = EnvFile::create([
            'user_id' => $user->id,
            'name' => 'Test Env',
            'project_name' => 'Test Project',
            'content' => 'TEST=value',
            'environment' => 'production',
        ]);

        $this->assertTrue($user->can('view', $envFile));
    }

    /**
     * Test that users cannot view other users' env files
     */
    public function test_user_cannot_view_other_users_env_file(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        
        $envFile = EnvFile::create([
            'user_id' => $user1->id,
            'name' => 'Test Env',
            'project_name' => 'Test Project',
            'content' => 'TEST=value',
            'environment' => 'production',
        ]);

        $this->assertFalse($user2->can('view', $envFile));
    }

    /**
     * Test that users can create env files
     */
    public function test_user_can_create_env_files(): void
    {
        $user = User::factory()->create();
        
        $this->assertTrue($user->can('create', EnvFile::class));
    }

    /**
     * Test that users can update their own env files
     */
    public function test_user_can_update_own_env_file(): void
    {
        $user = User::factory()->create();
        
        $envFile = EnvFile::create([
            'user_id' => $user->id,
            'name' => 'Test Env',
            'project_name' => 'Test Project',
            'content' => 'TEST=value',
            'environment' => 'production',
        ]);

        $this->assertTrue($user->can('update', $envFile));
    }

    /**
     * Test that users cannot update other users' env files
     */
    public function test_user_cannot_update_other_users_env_file(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        
        $envFile = EnvFile::create([
            'user_id' => $user1->id,
            'name' => 'Test Env',
            'project_name' => 'Test Project',
            'content' => 'TEST=value',
            'environment' => 'production',
        ]);

        $this->assertFalse($user2->can('update', $envFile));
    }

    /**
     * Test that users can delete their own env files
     */
    public function test_user_can_delete_own_env_file(): void
    {
        $user = User::factory()->create();
        
        $envFile = EnvFile::create([
            'user_id' => $user->id,
            'name' => 'Test Env',
            'project_name' => 'Test Project',
            'content' => 'TEST=value',
            'environment' => 'production',
        ]);

        $this->assertTrue($user->can('delete', $envFile));
    }

    /**
     * Test that users cannot delete other users' env files
     */
    public function test_user_cannot_delete_other_users_env_file(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        
        $envFile = EnvFile::create([
            'user_id' => $user1->id,
            'name' => 'Test Env',
            'project_name' => 'Test Project',
            'content' => 'TEST=value',
            'environment' => 'production',
        ]);

        $this->assertFalse($user2->can('delete', $envFile));
    }

    /**
     * Test that users can restore their own env files
     */
    public function test_user_can_restore_own_env_file(): void
    {
        $user = User::factory()->create();
        
        $envFile = EnvFile::create([
            'user_id' => $user->id,
            'name' => 'Test Env',
            'project_name' => 'Test Project',
            'content' => 'TEST=value',
            'environment' => 'production',
        ]);

        $this->assertTrue($user->can('restore', $envFile));
    }

    /**
     * Test that users cannot restore other users' env files
     */
    public function test_user_cannot_restore_other_users_env_file(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        
        $envFile = EnvFile::create([
            'user_id' => $user1->id,
            'name' => 'Test Env',
            'project_name' => 'Test Project',
            'content' => 'TEST=value',
            'environment' => 'production',
        ]);

        $this->assertFalse($user2->can('restore', $envFile));
    }

    /**
     * Test that users can force delete their own env files
     */
    public function test_user_can_force_delete_own_env_file(): void
    {
        $user = User::factory()->create();
        
        $envFile = EnvFile::create([
            'user_id' => $user->id,
            'name' => 'Test Env',
            'project_name' => 'Test Project',
            'content' => 'TEST=value',
            'environment' => 'production',
        ]);

        $this->assertTrue($user->can('forceDelete', $envFile));
    }

    /**
     * Test that users cannot force delete other users' env files
     */
    public function test_user_cannot_force_delete_other_users_env_file(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        
        $envFile = EnvFile::create([
            'user_id' => $user1->id,
            'name' => 'Test Env',
            'project_name' => 'Test Project',
            'content' => 'TEST=value',
            'environment' => 'production',
        ]);

        $this->assertFalse($user2->can('forceDelete', $envFile));
    }
}
