<?php

namespace App\Models;

use App\Enums\ActivationStatus;
use App\Scopes\ActivationBrandScope;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Activation extends Model
{
    use HasFactory;
    use HasUuid;

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new ActivationBrandScope());
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'license_key_id',
        'license_id',
        'device_identifier',
        'device_name',
        'instance_type',
        'instance_value',
        'ip_address',
        'user_agent',
        'status',
        'activated_at',
        'deactivated_at',
        'last_checked_at',
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => \App\Enums\ActivationStatus::class,
        'activated_at' => 'datetime',
        'deactivated_at' => 'datetime',
        'last_checked_at' => 'datetime',
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'device_identifier' => 'encrypted', // Encrypt device identifiers (PII)
        'ip_address' => 'encrypted', // Encrypt IP addresses (PII)
    ];

    /**
     * Get the license key that owns the activation.
     */
    public function licenseKey(): BelongsTo
    {
        return $this->belongsTo(LicenseKey::class);
    }

    /**
     * Get the license that owns the activation.
     */
    public function license(): BelongsTo
    {
        return $this->belongsTo(License::class);
    }

    /**
     * Scope a query to only include active activations.
     */
    public function scopeActive($query)
    {
        return $query->where('status', ActivationStatus::ACTIVE);
    }

    /**
     * Deactivate this activation.
     */
    public function deactivate(): void
    {
        $this->update([
            'status' => ActivationStatus::INACTIVE,
            'deactivated_at' => now(),
        ]);
    }

    /**
     * Update last checked timestamp.
     */
    public function updateLastChecked(): void
    {
        $this->update(['last_checked_at' => now()]);
    }
}
