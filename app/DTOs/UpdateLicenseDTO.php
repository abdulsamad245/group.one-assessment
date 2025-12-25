<?php

namespace App\DTOs;

use App\Traits\DTOToArray;

class UpdateLicenseDTO
{
    use DTOToArray;

    private ?string $customer_email = null;
    private ?string $customer_name = null;
    private ?string $product_name = null;
    private ?string $product_sku = null;
    private ?string $license_type = null;
    private ?int $max_activations = null;
    private ?string $expires_at = null;
    private ?string $status = null;
    private ?array $metadata = null;

    public function setCustomerEmail(?string $customer_email): self
    {
        $this->customer_email = $customer_email;
        return $this;
    }

    public function getCustomerEmail(): ?string
    {
        return $this->customer_email;
    }

    public function setCustomerName(?string $customer_name): self
    {
        $this->customer_name = $customer_name;
        return $this;
    }

    public function getCustomerName(): ?string
    {
        return $this->customer_name;
    }

    public function setProductName(?string $product_name): self
    {
        $this->product_name = $product_name;
        return $this;
    }

    public function getProductName(): ?string
    {
        return $this->product_name;
    }

    public function setProductSku(?string $product_sku): self
    {
        $this->product_sku = $product_sku;
        return $this;
    }

    public function getProductSku(): ?string
    {
        return $this->product_sku;
    }

    public function setLicenseType(?string $license_type): self
    {
        $this->license_type = $license_type;
        return $this;
    }

    public function getLicenseType(): ?string
    {
        return $this->license_type;
    }

    public function setMaxActivations(?int $max_activations): self
    {
        $this->max_activations = $max_activations;
        return $this;
    }

    public function getMaxActivations(): ?int
    {
        return $this->max_activations;
    }

    public function setExpiresAt(?string $expires_at): self
    {
        $this->expires_at = $expires_at;
        return $this;
    }

    public function getExpiresAt(): ?string
    {
        return $this->expires_at;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
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
