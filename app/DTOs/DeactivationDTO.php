<?php

namespace App\DTOs;

use App\Traits\DTOToArray;

class DeactivationDTO
{
    use DTOToArray;

    private string $activation_id;

    public function setActivationId(string $activation_id): self
    {
        $this->activation_id = $activation_id;
        return $this;
    }

    public function getActivationId(): string
    {
        return $this->activation_id;
    }
}
