<?php

namespace Tests\Feature;

use App\Models\EnvFile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EnvFileSecurityTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that env file content is encrypted in database
     */
    public function test_env_file_content_is_encrypted_in_database(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $plainContent = "APP_KEY=secret123\nDB_PASSWORD=mypassword";
        
        $envFile = EnvFile::create([
            'user_id' => $user->id,
            'name' => 'Test Env',
            'project_name' => 'Test Project',
            'content' => $plainContent,
            'environment' => 'production',
        ]);

        // Get raw database value
        $rawContent = \DB::table('env_files')
            ->where('id', $envFile->id)
            ->value('content');

        // The raw content should NOT match the plain content
        $this->assertNotEquals($plainContent, $rawContent);
        
        // The raw content should be encrypted (not readable)
        $this->assertStringNotContainsString('APP_KEY', $rawContent);
        $this->assertStringNotContainsString('secret123', $rawContent);
        $this->assertStringNotContainsString('DB_PASSWORD', $rawContent);
    }

    /**
     * Test that env file content is properly decrypted when accessed
     */
    public function test_env_file_content_is_decrypted_when_accessed(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $plainContent = "APP_KEY=secret123\nDB_PASSWORD=mypassword";
        
        $envFile = EnvFile::create([
            'user_id' => $user->id,
            'name' => 'Test Env',
            'project_name' => 'Test Project',
            'content' => $plainContent,
            'environment' => 'production',
        ]);

        // When we access through the model, it should be decrypted
        $this->assertEquals($plainContent, $envFile->content);
        $this->assertStringContainsString('APP_KEY=secret123', $envFile->content);
    }

    /**
     * Test that users cannot access other users' env files
     */
    public function test_user_cannot_view_other_users_env_files(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $envFile = EnvFile::create([
            'user_id' => $user1->id,
            'name' => 'User 1 Env',
            'project_name' => 'Project 1',
            'content' => 'SECRET=user1secret',
            'environment' => 'production',
        ]);

        // User 2 tries to access User 1's env file
        $this->actingAs($user2);
        
        $response = $this->get(route('env-files.show', $envFile));
        
        // Should be forbidden
        $response->assertForbidden();
    }

    /**
     * Test that users cannot edit other users' env files
     */
    public function test_user_cannot_edit_other_users_env_files(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $envFile = EnvFile::create([
            'user_id' => $user1->id,
            'name' => 'User 1 Env',
            'project_name' => 'Project 1',
            'content' => 'SECRET=user1secret',
            'environment' => 'production',
        ]);

        // User 2 tries to edit User 1's env file
        $this->actingAs($user2);
        
        $response = $this->get(route('env-files.edit', $envFile));
        
        // Should be forbidden
        $response->assertForbidden();
    }

    /**
     * Test that users cannot delete other users' env files
     */
    public function test_user_cannot_delete_other_users_env_files(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $envFile = EnvFile::create([
            'user_id' => $user1->id,
            'name' => 'User 1 Env',
            'project_name' => 'Project 1',
            'content' => 'SECRET=user1secret',
            'environment' => 'production',
        ]);

        $this->actingAs($user2);

        // Try to delete via Livewire component
        $response = \Livewire\Livewire::test(\App\Livewire\EnvFiles\Index::class)
            ->call('delete', $envFile->id);

        // Should fail - env file should still exist
        $this->assertDatabaseHas('env_files', [
            'id' => $envFile->id,
        ]);
    }

    /**
     * Test that unauthenticated users cannot access env files
     */
    public function test_unauthenticated_users_cannot_access_env_files(): void
    {
        $user = User::factory()->create();
        
        $envFile = EnvFile::create([
            'user_id' => $user->id,
            'name' => 'Test Env',
            'project_name' => 'Test Project',
            'content' => 'SECRET=mysecret',
            'environment' => 'production',
        ]);

        // Try to access without authentication
        $response = $this->get(route('env-files.index'));
        $response->assertRedirect(route('login'));

        $response = $this->get(route('env-files.show', $envFile));
        $response->assertRedirect(route('login'));

        $response = $this->get(route('env-files.create'));
        $response->assertRedirect(route('login'));

        $response = $this->get(route('env-files.edit', $envFile));
        $response->assertRedirect(route('login'));
    }

    /**
     * Test that XSS attempts in env file name are escaped
     */
    public function test_xss_attempts_are_escaped(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $xssAttempt = '<script>alert("XSS")</script>';
        
        $envFile = EnvFile::create([
            'user_id' => $user->id,
            'name' => $xssAttempt,
            'project_name' => 'Test Project',
            'description' => $xssAttempt,
            'content' => 'TEST=value',
            'environment' => 'production',
        ]);

        $response = $this->get(route('env-files.show', $envFile));
        
        // The raw script tag should not be in the response
        $response->assertDontSee('<script>alert("XSS")</script>', false);
        // But the escaped version should be
        $response->assertSee('&lt;script&gt;alert(&quot;XSS&quot;)&lt;/script&gt;', false);
    }

    /**
     * Test that SQL injection attempts in search are safe
     */
    public function test_sql_injection_in_search_is_prevented(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create a normal env file
        EnvFile::create([
            'user_id' => $user->id,
            'name' => 'Normal Env',
            'project_name' => 'Normal Project',
            'content' => 'TEST=value',
            'environment' => 'production',
        ]);

        // Try SQL injection in search
        $sqlInjection = "' OR '1'='1";
        
        $response = \Livewire\Livewire::test(\App\Livewire\EnvFiles\Index::class)
            ->set('search', $sqlInjection);

        // Should not cause an error and should be safely handled
        $response->assertStatus(200);
    }

    /**
     * Test that only user's own files are listed in index
     */
    public function test_user_only_sees_own_env_files(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        // Create env files for both users
        $envFile1 = EnvFile::create([
            'user_id' => $user1->id,
            'name' => 'User 1 Env',
            'project_name' => 'Project 1',
            'content' => 'SECRET=user1secret',
            'environment' => 'production',
        ]);

        $envFile2 = EnvFile::create([
            'user_id' => $user2->id,
            'name' => 'User 2 Env',
            'project_name' => 'Project 2',
            'content' => 'SECRET=user2secret',
            'environment' => 'production',
        ]);

        // User 1 should only see their own files
        $this->actingAs($user1);
        $response = $this->get(route('env-files.index'));
        $response->assertSee('User 1 Env');
        $response->assertDontSee('User 2 Env');

        // User 2 should only see their own files
        $this->actingAs($user2);
        $response = $this->get(route('env-files.index'));
        $response->assertSee('User 2 Env');
        $response->assertDontSee('User 1 Env');
    }

    /**
     * Test that download authorization is properly enforced
     */
    public function test_download_requires_authorization(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $envFile = EnvFile::create([
            'user_id' => $user1->id,
            'name' => 'Test Env',
            'project_name' => 'Test Project',
            'content' => 'SECRET=mysecret',
            'environment' => 'production',
        ]);

        // User 2 tries to download User 1's file
        $this->actingAs($user2);
        
        $component = \Livewire\Livewire::test(\App\Livewire\EnvFiles\Show::class, ['envFile' => $envFile]);
        
        // The component mount should fail with authorization error
        $component->assertForbidden();
    }

    /**
     * Test input validation for required fields
     */
    public function test_required_fields_are_validated(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $component = \Livewire\Livewire::test(\App\Livewire\EnvFiles\Create::class)
            ->set('name', '')
            ->set('project_name', '')
            ->set('content', '')
            ->call('save');

        $component->assertHasErrors(['name', 'project_name', 'content']);
    }

    /**
     * Test environment field validation
     */
    public function test_environment_field_is_validated(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $component = \Livewire\Livewire::test(\App\Livewire\EnvFiles\Create::class)
            ->set('name', 'Test')
            ->set('project_name', 'Test Project')
            ->set('content', 'TEST=value')
            ->set('environment', 'invalid_environment')
            ->call('save');

        $component->assertHasErrors(['environment']);
    }

    /**
     * Test that env file content with special characters is handled correctly
     */
    public function test_special_characters_in_content_are_preserved(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $specialContent = "APP_KEY=base64:abc123/+=\nDB_PASS='special\"chars'\nURL=https://example.com?param=value&other=test";
        
        $envFile = EnvFile::create([
            'user_id' => $user->id,
            'name' => 'Test Env',
            'project_name' => 'Test Project',
            'content' => $specialContent,
            'environment' => 'production',
        ]);

        // Refresh from database
        $envFile->refresh();

        // Special characters should be preserved after encryption/decryption
        $this->assertEquals($specialContent, $envFile->content);
    }
}
