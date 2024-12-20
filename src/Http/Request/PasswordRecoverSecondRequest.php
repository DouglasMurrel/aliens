<?php

namespace App\Http\Request;

use Symfony\Component\Validator\Constraints as Assert;

class PasswordRecoverSecondRequest {
    #[Assert\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 8,
    )]
    private $password;
   
    public function getPassword() {
        return $this->password;
    }

    public function setPassword($password): self
    {
        $this->password = $password;
        return $this;
    }
}
