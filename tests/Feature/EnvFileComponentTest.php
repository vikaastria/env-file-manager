<?php

namespace Tests\Feature;

use App\Models\EnvFile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class EnvFileComponentTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test creating an env file through Livewire component
     */
    public function test_can_create_env_file(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Livewire::test(\App\Livewire\EnvFiles\Create::class)
            ->set('name', 'Production Config')
            ->set('project_name', 'My App')
            ->set('description', 'Production environment configuration')
            ->set('content', "APP_ENV=production\nAPP_KEY=base64:test123")
            ->set('environment', 'production')
            ->call('save')
            ->assertRedirect(route('env-files.index'));

        $this->assertDatabaseHas('env_files', [
            'user_id' => $user->id,
            'name' => 'Production Config',
            'project_name' => 'My App',
            'environment' => 'production',
        ]);
    }

    /**
     * Test editing an env file through Livewire component
     */
    public function test_can_edit_env_file(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $envFile = EnvFile::create([
            'user_id' => $user->id,
            'name' => 'Original Name',
            'project_name' => 'Original Project',
            'content' => 'ORIGINAL=value',
            'environment' => 'development',
        ]);

        Livewire::test(\App\Livewire\EnvFiles\Edit::class, ['envFile' => $envFile])
            ->set('name', 'Updated Name')
            ->set('project_name', 'Updated Project')
            ->set('content', 'UPDATED=value')
            ->set('environment', 'production')
            ->call('save')
            ->assertRedirect(route('env-files.index'));

        $envFile->refresh();

        $this->assertEquals('Updated Name', $envFile->name);
        $this->assertEquals('Updated Project', $envFile->project_name);
        $this->assertEquals('UPDATED=value', $envFile->content);
        $this->assertEquals('production', $envFile->environment);
    }

    /**
     * Test deleting an env file through Livewire component
     */
    public function test_can_delete_env_file(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $envFile = EnvFile::create([
            'user_id' => $user->id,
            'name' => 'Test Env',
            'project_name' => 'Test Project',
            'content' => 'TEST=value',
            'environment' => 'production',
        ]);

        Livewire::test(\App\Livewire\EnvFiles\Index::class)
            ->call('delete', $envFile->id);

        $this->assertDatabaseMissing('env_files', [
            'id' => $envFile->id,
        ]);
    }

    /**
     * Test search functionality in index component
     */
    public function test_can_search_env_files(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        EnvFile::create([
            'user_id' => $user->id,
            'name' => 'Production Config',
            'project_name' => 'Main App',
            'content' => 'TEST=value',
            'environment' => 'production',
        ]);

        EnvFile::create([
            'user_id' => $user->id,
            'name' => 'Development Config',
            'project_name' => 'Test App',
            'content' => 'TEST=value',
            'environment' => 'development',
        ]);

        $component = Livewire::test(\App\Livewire\EnvFiles\Index::class)
            ->set('search', 'Production');

        $component->assertSee('Production Config');
        $component->assertDontSee('Development Config');
    }

    /**
     * Test environment filter in index component
     */
    public function test_can_filter_by_environment(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        EnvFile::create([
            'user_id' => $user->id,
            'name' => 'Production Config',
            'project_name' => 'Main App',
            'content' => 'TEST=value',
            'environment' => 'production',
        ]);

        EnvFile::create([
            'user_id' => $user->id,
            'name' => 'Development Config',
            'project_name' => 'Test App',
            'content' => 'TEST=value',
            'environment' => 'development',
        ]);

        $component = Livewire::test(\App\Livewire\EnvFiles\Index::class)
            ->set('environment', 'production');

        $component->assertSee('Production Config');
        $component->assertDontSee('Development Config');
    }

    /**
     * Test toggling content visibility in show component
     */
    public function test_can_toggle_content_visibility(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $envFile = EnvFile::create([
            'user_id' => $user->id,
            'name' => 'Test Env',
            'project_name' => 'Test Project',
            'content' => 'SECRET_KEY=mysecret',
            'environment' => 'production',
        ]);

        $component = Livewire::test(\App\Livewire\EnvFiles\Show::class, ['envFile' => $envFile]);

        // Initially content should be hidden
        $this->assertFalse($component->get('showContent'));

        // Toggle to show
        $component->call('toggleContent');
        $this->assertTrue($component->get('showContent'));

        // Toggle to hide
        $component->call('toggleContent');
        $this->assertFalse($component->get('showContent'));
    }

    /**
     * Test validation errors are shown for invalid input
     */
    public function test_validation_errors_are_shown(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Livewire::test(\App\Livewire\EnvFiles\Create::class)
            ->set('name', '') // Required field
            ->set('project_name', '')
            ->set('content', '')
            ->set('environment', 'invalid') // Invalid option
            ->call('save')
            ->assertHasErrors([
                'name' => 'required',
                'project_name' => 'required',
                'content' => 'required',
                'environment' => 'in',
            ]);
    }

    /**
     * Test max length validation
     */
    public function test_max_length_validation(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $longString = str_repeat('a', 256); // Exceeds max:255

        Livewire::test(\App\Livewire\EnvFiles\Create::class)
            ->set('name', $longString)
            ->set('project_name', $longString)
            ->set('content', 'TEST=value')
            ->set('environment', 'production')
            ->call('save')
            ->assertHasErrors([
                'name' => 'max',
                'project_name' => 'max',
            ]);
    }

    /**
     * Test that session flash message is shown after creation
     */
    public function test_flash_message_after_creation(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Livewire::test(\App\Livewire\EnvFiles\Create::class)
            ->set('name', 'Test Env')
            ->set('project_name', 'Test Project')
            ->set('content', 'TEST=value')
            ->set('environment', 'production')
            ->call('save')
            ->assertSessionHas('message', 'Env file created successfully.');
    }

    /**
     * Test that session flash message is shown after update
     */
    public function test_flash_message_after_update(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $envFile = EnvFile::create([
            'user_id' => $user->id,
            'name' => 'Test Env',
            'project_name' => 'Test Project',
            'content' => 'TEST=value',
            'environment' => 'production',
        ]);

        Livewire::test(\App\Livewire\EnvFiles\Edit::class, ['envFile' => $envFile])
            ->set('name', 'Updated Name')
            ->call('save')
            ->assertSessionHas('message', 'Env file updated successfully.');
    }

    /**
     * Test that session flash message is shown after deletion
     */
    public function test_flash_message_after_deletion(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $envFile = EnvFile::create([
            'user_id' => $user->id,
            'name' => 'Test Env',
            'project_name' => 'Test Project',
            'content' => 'TEST=value',
            'environment' => 'production',
        ]);

        Livewire::test(\App\Livewire\EnvFiles\Index::class)
            ->call('delete', $envFile->id)
            ->assertSessionHas('message', 'Env file deleted successfully.');
    }

    /**
     * Test pagination in index
     */
    public function test_pagination_works(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create 15 env files (more than the per-page limit of 10)
        for ($i = 1; $i <= 15; $i++) {
            EnvFile::create([
                'user_id' => $user->id,
                'name' => "Env File $i",
                'project_name' => "Project $i",
                'content' => 'TEST=value',
                'environment' => 'production',
            ]);
        }

        $component = Livewire::test(\App\Livewire\EnvFiles\Index::class);

        // Should see first 10 items
        $component->assertSee('Env File 1');
        $component->assertDontSee('Env File 15');

        // Navigate to page 2
        $component->set('page', 2);
        $component->assertSee('Env File 15');
    }

    /**
     * Test that unauthorized users cannot mount edit component
     */
    public function test_unauthorized_user_cannot_mount_edit_component(): void
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

        $this->actingAs($user2);

        Livewire::test(\App\Livewire\EnvFiles\Edit::class, ['envFile' => $envFile])
            ->assertForbidden();
    }

    /**
     * Test that unauthorized users cannot mount show component
     */
    public function test_unauthorized_user_cannot_mount_show_component(): void
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

        $this->actingAs($user2);

        Livewire::test(\App\Livewire\EnvFiles\Show::class, ['envFile' => $envFile])
            ->assertForbidden();
    }
}
