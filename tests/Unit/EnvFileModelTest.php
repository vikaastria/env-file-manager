<?php

namespace Tests\Unit;

use App\Models\EnvFile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Tests\TestCase;

class EnvFileModelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that user relationship is properly defined
     */
    public function test_env_file_belongs_to_user(): void
    {
        $user = User::factory()->create();
        
        $envFile = EnvFile::create([
            'user_id' => $user->id,
            'name' => 'Test Env',
            'project_name' => 'Test Project',
            'content' => 'TEST=value',
            'environment' => 'production',
        ]);

        $this->assertInstanceOf(User::class, $envFile->user);
        $this->assertEquals($user->id, $envFile->user->id);
    }

    /**
     * Test that content is automatically encrypted on save
     */
    public function test_content_is_encrypted_on_save(): void
    {
        $user = User::factory()->create();
        $plainContent = 'APP_KEY=secret123';

        $envFile = EnvFile::create([
            'user_id' => $user->id,
            'name' => 'Test Env',
            'project_name' => 'Test Project',
            'content' => $plainContent,
            'environment' => 'production',
        ]);

        // Get raw value from database
        $rawValue = \DB::table('env_files')
            ->where('id', $envFile->id)
            ->value('content');

        // Raw value should be encrypted
        $this->assertNotEquals($plainContent, $rawValue);
        
        // We should be able to decrypt it manually
        $decrypted = Crypt::decryptString($rawValue);
        $this->assertEquals($plainContent, $decrypted);
    }

    /**
     * Test that content is automatically decrypted on retrieval
     */
    public function test_content_is_decrypted_on_retrieval(): void
    {
        $user = User::factory()->create();
        $plainContent = 'APP_KEY=secret123';

        $envFile = EnvFile::create([
            'user_id' => $user->id,
            'name' => 'Test Env',
            'project_name' => 'Test Project',
            'content' => $plainContent,
            'environment' => 'production',
        ]);

        // Refresh from database
        $envFile->refresh();

        // Content should be automatically decrypted
        $this->assertEquals($plainContent, $envFile->content);
    }

    /**
     * Test that fillable attributes are correctly defined
     */
    public function test_fillable_attributes(): void
    {
        $envFile = new EnvFile();
        
        $expectedFillable = [
            'user_id',
            'name',
            'project_name',
            'description',
            'content',
            'environment',
        ];

        $this->assertEquals($expectedFillable, $envFile->getFillable());
    }

    /**
     * Test that timestamps are cast to datetime
     */
    public function test_timestamps_are_cast_to_datetime(): void
    {
        $user = User::factory()->create();

        $envFile = EnvFile::create([
            'user_id' => $user->id,
            'name' => 'Test Env',
            'project_name' => 'Test Project',
            'content' => 'TEST=value',
            'environment' => 'production',
        ]);

        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $envFile->created_at);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $envFile->updated_at);
    }

    /**
     * Test that empty content can be encrypted and decrypted
     */
    public function test_empty_content_is_handled(): void
    {
        $user = User::factory()->create();

        $envFile = EnvFile::create([
            'user_id' => $user->id,
            'name' => 'Test Env',
            'project_name' => 'Test Project',
            'content' => '',
            'environment' => 'production',
        ]);

        $this->assertEquals('', $envFile->content);
    }

    /**
     * Test that multiline content is preserved
     */
    public function test_multiline_content_is_preserved(): void
    {
        $user = User::factory()->create();
        
        $multilineContent = "APP_NAME=MyApp\nAPP_ENV=production\nAPP_KEY=base64:test123\nDB_HOST=localhost";

        $envFile = EnvFile::create([
            'user_id' => $user->id,
            'name' => 'Test Env',
            'project_name' => 'Test Project',
            'content' => $multilineContent,
            'environment' => 'production',
        ]);

        $envFile->refresh();

        $this->assertEquals($multilineContent, $envFile->content);
        $this->assertStringContainsString("\n", $envFile->content);
    }

    /**
     * Test that content with unicode characters is preserved
     */
    public function test_unicode_content_is_preserved(): void
    {
        $user = User::factory()->create();
        
        $unicodeContent = "APP_NAME=Tëst Âpp 你好 🚀\nSPECIAL_CHAR=ñáéíóú";

        $envFile = EnvFile::create([
            'user_id' => $user->id,
            'name' => 'Test Env',
            'project_name' => 'Test Project',
            'content' => $unicodeContent,
            'environment' => 'production',
        ]);

        $envFile->refresh();

        $this->assertEquals($unicodeContent, $envFile->content);
    }

    /**
     * Test that updating content re-encrypts it
     */
    public function test_updating_content_re_encrypts(): void
    {
        $user = User::factory()->create();

        $envFile = EnvFile::create([
            'user_id' => $user->id,
            'name' => 'Test Env',
            'project_name' => 'Test Project',
            'content' => 'ORIGINAL=value',
            'environment' => 'production',
        ]);

        $originalRaw = \DB::table('env_files')
            ->where('id', $envFile->id)
            ->value('content');

        // Update content
        $envFile->update(['content' => 'UPDATED=value']);

        $updatedRaw = \DB::table('env_files')
            ->where('id', $envFile->id)
            ->value('content');

        // Raw values should be different (re-encrypted)
        $this->assertNotEquals($originalRaw, $updatedRaw);
        
        // But decrypted value should match
        $this->assertEquals('UPDATED=value', $envFile->fresh()->content);
    }

    /**
     * Test cascade delete when user is deleted
     */
    public function test_env_files_are_deleted_when_user_is_deleted(): void
    {
        $user = User::factory()->create();

        $envFile = EnvFile::create([
            'user_id' => $user->id,
            'name' => 'Test Env',
            'project_name' => 'Test Project',
            'content' => 'TEST=value',
            'environment' => 'production',
        ]);

        $envFileId = $envFile->id;

        // Delete user
        $user->delete();

        // Env file should also be deleted (cascade)
        $this->assertDatabaseMissing('env_files', [
            'id' => $envFileId,
        ]);
    }
}
