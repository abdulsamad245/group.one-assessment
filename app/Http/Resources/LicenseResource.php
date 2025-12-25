<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LicenseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'license_key' => new LicenseKeyResource($this->whenLoaded('licenseKey')),
            'brand_name' => $this->whenLoaded('brand', function ($brand) {
                return $brand->name;
            }),
            'customer_email' => $this->customer_email,
            'customer_name' => $this->customer_name,
            'product_name' => $this->product_name,
            'product_slug' => $this->product_slug,
            'product_sku' => $this->product_sku,
            'license_type' => $this->license_type,
            'max_activations_per_instance' => $this->max_activations_per_instance,
            'current_activations' => $this->current_activations,
            'expires_at' => $this->expires_at,
            'status' => $this->status,
            'is_expired' => $this->isExpired(),
            'can_activate' => $this->canActivate(),
            'metadata' => $this->metadata,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
