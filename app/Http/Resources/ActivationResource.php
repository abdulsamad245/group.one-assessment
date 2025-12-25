<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivationResource extends JsonResource
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
            'instance_type' => $this->instance_type,
            'instance_value' => $this->instance_value,
            'device_identifier' => $this->device_identifier,
            'device_name' => $this->device_name,
            'ip_address' => $this->ip_address,
            'status' => $this->status,
            'activated_at' => $this->activated_at,
            'deactivated_at' => $this->deactivated_at,
            'last_checked_at' => $this->last_checked_at,
            'metadata' => $this->metadata,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'license_key' => new LicenseKeyResource($this->whenLoaded('licenseKey')),
            'license' => new LicenseResource($this->whenLoaded('license')),
        ];
    }
}
