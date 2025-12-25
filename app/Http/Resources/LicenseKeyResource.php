<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LicenseKeyResource extends JsonResource
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
            'brand_id' => $this->brand_id,
            'customer_email' => $this->customer_email,
            'key' => $this->key,
            'status' => $this->status,
            'expires_at' => $this->expires_at,
            'is_valid' => $this->isValid(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            // Include full license details when loaded
            'licenses' => LicenseResource::collection($this->whenLoaded('licenses')),

            // Include activation details when loaded
            'activations' => ActivationResource::collection($this->whenLoaded('activations')),

            // Include activation count when activations are loaded
            'activations_count' => $this->when($this->relationLoaded('activations'), function () {
                return $this->activations->where('status', 'active')->count();
            }),

            'brand_name' => $this->whenLoaded('brand', function ($brand) {
                return $brand->name;
            }),

            // Calculate remaining seats per instance across all licenses
            'entitlements' => $this->when($this->relationLoaded('licenses'), function () {
                $entitlements = [];

                foreach ($this->licenses as $license) {
                    $productEntitlements = [];

                    foreach ($license->max_activations_per_instance as $instanceType => $maxActivations) {
                        $activeActivations = $this->activations
                            ->where('license_id', $license->id)
                            ->where('instance_type', $instanceType)
                            ->where('status', 'active')
                            ->unique('instance_value')
                            ->count();

                        $productEntitlements[$instanceType] = [
                            'max_activations' => $maxActivations,
                            'active_activations' => $activeActivations,
                            'remaining_seats' => max(0, $maxActivations - $activeActivations),
                        ];
                    }

                    $entitlements[] = [
                        'product_name' => $license->product_name,
                        'product_slug' => $license->product_slug,
                        'product_sku' => $license->product_sku,
                        'license_type' => $license->license_type,
                        'license_status' => $license->status,
                        'license_expires_at' => $license->expires_at,
                        'instances' => $productEntitlements,
                    ];
                }

                return $entitlements;
            }),
        ];
    }
}
