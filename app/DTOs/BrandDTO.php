<?php

namespace App\DTOs;

use App\Traits\DTOToArray;

class BrandDTO
{
    use DTOToArray;

    private string $name;
    private string $slug;
    private ?string $description = null;
    private ?string $contact_email = null;
    private ?string $website = null;
    private ?array $settings = null;
    private bool $is_active = true;

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;
        return $this;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setContactEmail(?string $contact_email): self
    {
        $this->contact_email = $contact_email;
        return $this;
    }

    public function getContactEmail(): ?string
    {
        return $this->contact_email;
    }

    public function setWebsite(?string $website): self
    {
        $this->website = $website;
        return $this;
    }

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setSettings(?array $settings): self
    {
        $this->settings = $settings;
        return $this;
    }

    public function getSettings(): ?array
    {
        return $this->settings;
    }

    public function setIsActive(bool $is_active): self
    {
        $this->is_active = $is_active;
        return $this;
    }

    public function getIsActive(): bool
    {
        return $this->is_active;
    }
}
