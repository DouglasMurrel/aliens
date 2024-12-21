<?php

namespace App\Http\Request;

use Symfony\Component\Validator\Constraints as Assert;

class PasswordRecoverRequest
{
    #[Assert\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Email]
    private $email;
    
    public function getEmail(): string
    {
        return $this->email;
    }
    
    public function setEmail($email): self {
        $this->email = $email;
        return $this;
    }
}
