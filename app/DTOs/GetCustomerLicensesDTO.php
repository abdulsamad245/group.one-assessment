<?php

namespace App\DTOs;

use App\Traits\DTOToArray;

class GetCustomerLicensesDTO
{
    use DTOToArray;

    private string $email;

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}
