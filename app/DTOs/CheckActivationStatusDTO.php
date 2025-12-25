<?php

namespace App\DTOs;

use App\Traits\DTOToArray;

class CheckActivationStatusDTO
{
    use DTOToArray;

    private string $license_key;
    private string $product_slug;

    public function setLicenseKey(string $license_key): self
    {
        $this->license_key = $license_key;
        return $this;
    }

    public function getLicenseKey(): string
    {
        return $this->license_key;
    }

    public function setProductSlug(string $product_slug): self
    {
        $this->product_slug = $product_slug;
        return $this;
    }

    public function getProductSlug(): string
    {
        return $this->product_slug;
    }
}
