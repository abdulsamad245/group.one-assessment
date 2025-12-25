<?php

namespace App\DTOs;

use App\Traits\DTOToArray;

class CreateApiKeyDTO
{
    use DTOToArray;

    private string $brand_id;
    private string $name;
    private ?array $permissions = null;

    public function setBrandId(string $brand_id): self
    {
        $this->brand_id = $brand_id;
        return $this;
    }

    public function getBrandId(): string
    {
        return $this->brand_id;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setPermissions(?array $permissions): self
    {
        $this->permissions = $permissions;
        return $this;
    }

    public function getPermissions(): ?array
    {
        return $this->permissions;
    }
}
