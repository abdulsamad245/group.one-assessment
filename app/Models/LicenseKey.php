<?php

namespace App\Models;

use App\Enums\ActivationStatus;
use App\Enums\LicenseKeyStatus;
use App\Scopes\LicenseKeyBrandScope;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class LicenseKey extends Model
{
    use HasFactory;
    use SoftDeletes;
    use HasUuid;

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new LicenseKeyBrandScope());
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'brand_id',
        'customer_email',
        'key',
        'key_hash',
        'status',
        'expires_at',
        'metadata',
    ];

    /**
     * The attributes that should be encrypted.
     *
     * @var array<int, string>
     */
    protected $encryptable = [
        'key',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'activated_at' => 'datetime',
        'expires_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'key' => 'encrypted', // Encrypt license keys
        'status' => LicenseKeyStatus::class,
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => 'active',
    ];

    /**
     * Get the brand that owns the license key.
     */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * Get all licenses associated with this license key.
     */
    public function licenses(): HasMany
    {
        return $this->hasMany(License::class);
    }

    /**
     * Get the activations for the license key.
     */
    public function activations(): HasMany
    {
        return $this->hasMany(Activation::class);
    }

    /**
     * Get active activations.
     */
    public function activeActivations(): HasMany
    {
        return $this->hasMany(Activation::class)->where('status', ActivationStatus::ACTIVE);
    }

    /**
     * Scope a query to only include active license keys.
     */
    public function scopeActive($query)
    {
        return $query->where('status', LicenseKeyStatus::ACTIVE);
    }

    /**
     * Check if the license key is valid.
     */
    public function isValid(): bool
    {
        return $this->status === LicenseKeyStatus::ACTIVE
            && (!$this->expires_at || $this->expires_at->isFuture());
    }
}
