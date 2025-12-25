<?php

namespace App\DTOs;

use App\Traits\DTOToArray;

class ActivationDTO
{
    use DTOToArray;

    private string $license_key;
    private ?string $device_identifier = null;
    private ?string $device_name = null;
    private ?string $ip_address = null;
    private ?string $user_agent = null;
    private ?array $metadata = null;

    public function setLicenseKey(string $license_key): self
    {
        $this->license_key = $license_key;
        return $this;
    }

    public function getLicenseKey(): string
    {
        return $this->license_key;
    }

    public function setDeviceIdentifier(?string $device_identifier): self
    {
        $this->device_identifier = $device_identifier;
        return $this;
    }

    public function getDeviceIdentifier(): ?string
    {
        return $this->device_identifier;
    }

    public function setDeviceName(?string $device_name): self
    {
        $this->device_name = $device_name;
        return $this;
    }

    public function getDeviceName(): ?string
    {
        return $this->device_name;
    }

    public function setIpAddress(?string $ip_address): self
    {
        $this->ip_address = $ip_address;
        return $this;
    }

    public function getIpAddress(): ?string
    {
        return $this->ip_address;
    }

    public function setUserAgent(?string $user_agent): self
    {
        $this->user_agent = $user_agent;
        return $this;
    }

    public function getUserAgent(): ?string
    {
        return $this->user_agent;
    }

    public function setMetadata(?array $metadata): self
    {
        $this->metadata = $metadata;
        return $this;
    }

    public function getMetadata(): ?array
    {
        return $this->metadata;
    }
}
