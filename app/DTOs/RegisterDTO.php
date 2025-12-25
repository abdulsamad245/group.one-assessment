<?php

namespace App\DTOs;

use App\Traits\DTOToArray;

class RegisterDTO
{
    use DTOToArray;

    public function __construct(
        private string $name,
        private string $email,
        private string $password,
        private string $brandName,
        private string $brandSlug,
        private ?string $brandId = null,
        private string $role = 'user'
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getBrandName(): string
    {
        return $this->brandName;
    }

    public function getBrandSlug(): string
    {
        return $this->brandSlug;
    }

    public function getBrandId(): ?string
    {
        return $this->brandId;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function setBrandId(string $brandId): void
    {
        $this->brandId = $brandId;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function setBrandName(string $brandName): void
    {
        $this->brandName = $brandName;
    }

    public function setBrandSlug(string $brandSlug): void
    {
        $this->brandSlug = $brandSlug;
    }

    public function setRole(string $role): void
    {
        $this->role = $role;
    }
}
