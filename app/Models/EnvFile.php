<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Crypt;

class EnvFile extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'project_name',
        'description',
        'content',
        'environment',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns the env file.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Set the content attribute with encryption.
     */
    public function setContentAttribute($value): void
    {
        $this->attributes['content'] = Crypt::encryptString($value);
    }

    /**
     * Get the content attribute with decryption.
     */
    public function getContentAttribute($value): string
    {
        return Crypt::decryptString($value);
    }
}
