<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * API Key Model
 *
 * Represents an API key associated with a brand (tenant) for authentication.
 *
 * @property string $id
 * @property string $brand_id
 * @property string $name
 * @property string $key
 * @property string $prefix
 * @property array|null $permissions
 * @property \Illuminate\Support\Carbon|null $last_used_at
 * @property \Illuminate\Support\Carbon|null $expires_at
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read Brand|null $brand
 */
class ApiKey extends Model
{
    use HasFactory;
    use HasUuids;
    use SoftDeletes;

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The data type of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'brand_id',
        'name',
        'key',
        'prefix',
        'permissions',
        'last_used_at',
        'expires_at',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'permissions' => 'array',
        'last_used_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'key',
    ];

    /**
     * Get the brand that owns the API key.
     */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * Generate a new API key.
     *
     * @return string The plain text API key (prefix.secret)
     */
    public static function generate(): string
    {
        $prefix = 'lcs_' . Str::random(8);
        $secret = Str::random(40);

        return $prefix . '.' . $secret;
    }

    /**
     * Hash the API key for storage.
     *
     * @param string $plainKey The plain text API key
     * @return string The hashed key
     */
    public static function hash(string $plainKey): string
    {
        return hash('sha256', $plainKey);
    }

    /**
     * Extract the prefix from a plain API key.
     *
     * @param string $plainKey The plain text API key
     * @return string The prefix
     */
    public static function extractPrefix(string $plainKey): string
    {
        return explode('.', $plainKey)[0] ?? '';
    }

    /**
     * Check if the API key is valid (active and not expired).
     *
     * @return bool
     */
    public function isValid(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        return true;
    }

    /**
     * Update the last used timestamp.
     *
     * @return void
     */
    public function markAsUsed(): void
    {
        $this->last_used_at = now();
        $this->save();
    }

    /**
     * Scope a query to only include active API keys.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            });
    }
}
