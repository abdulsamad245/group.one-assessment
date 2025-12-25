<?php

namespace App\Models;

use App\Enums\LicenseStatus;
use App\Enums\LicenseType;
use App\Scopes\BrandScope;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class License extends Model
{
    use HasFactory;
    use SoftDeletes;
    use HasUuid;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'license_key_id',
        'brand_id',
        'customer_email',
        'customer_name',
        'product_name',
        'product_slug',
        'product_sku',
        'license_type',
        'max_activations_per_instance',
        'current_activations',
        'expires_at',
        'status',
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'max_activations_per_instance' => 'array',
        'current_activations' => 'integer',
        'expires_at' => 'datetime',
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'customer_email' => 'encrypted', // Encrypt customer email (PII)
        'status' => LicenseStatus::class,
        'license_type' => LicenseType::class,
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'current_activations' => 0,
        'status' => 'active',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new BrandScope());
    }

    /**
     * Get the brand that owns the license.
     */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * Get the license key that owns the license.
     */
    public function licenseKey(): BelongsTo
    {
        return $this->belongsTo(LicenseKey::class);
    }

    /**
     * Get the events for the license.
     */
    public function events(): HasMany
    {
        return $this->hasMany(LicenseEvent::class);
    }

    /**
     * Scope a query to only include active licenses.
     */
    public function scopeActive($query)
    {
        return $query->where('status', LicenseStatus::ACTIVE);
    }

    /**
     * Scope a query to filter by customer email.
     */
    public function scopeByCustomerEmail($query, string $email)
    {
        return $query->where('customer_email', $email);
    }

    /**
     * Check if the license is expired.
     */
    public function isExpired(): bool
    {
        if ($this->license_type === LicenseType::PERPETUAL) {
            return false;
        }

        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Check if the license can be activated.
     * Note: This is a basic check. Instance-specific limits are checked in ActivationService.
     */
    public function canActivate(): bool
    {
        return $this->status === LicenseStatus::ACTIVE
            && !$this->isExpired();
    }

    /**
     * Increment the activation count.
     */
    public function incrementActivations(): void
    {
        $this->increment('current_activations');
    }

    /**
     * Decrement the activation count.
     */
    public function decrementActivations(): void
    {
        $this->decrement('current_activations');
    }
}
