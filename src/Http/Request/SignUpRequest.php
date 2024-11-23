<?php

namespace App\Http\Request;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class SignUpRequest
{
    /**
     * @var string
     * @Serializer\Type("string")
     */
    #[Assert\Type('string')]
    #[Assert\NotBlank]
    private $fullname;

    /**
     * @var string
     * @Serializer\Type("string")
     */
    #[Assert\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Email]
    private $email;

    /**
     * @var string
     * @Serializer\Type("string")
     * @Assert\Type("string")
     * @Assert\NotBlank()
     * @PasswordStrength(minLength=8, minStrength=3)
     */
    #[Assert\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 8,
    )]
/*
    #[Assert\PasswordStrength([
         'minScore' => PasswordStrength::STRENGTH_WEAK
    ])]
 */
    private $password;

    public function getFullname(): string
    {
        return $this->fullname;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }
    
    public function setFullname($fullname): self {
        $this->fullname = $fullname;
        return $this;
    }

    public function setEmail($email): self {
        $this->email = $email;
        return $this;
    }

    public function setPassword($password): self {
        $this->password = $password;
        return $this;
    }


}