<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use App\Http\Request\SignUpRequest;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserCreator
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var UserPasswordHasherInterface
     */
    private $passwordEncoder;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordEncoder
    )
    {
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
    }

    public function createUser(SignUpRequest $signUpRequest): User
    {
        $user = new User();
        $user
            ->setEmail($signUpRequest->getEmail())
            ->setFullname($signUpRequest->getFullname())
            //->setPassword($this->passwordEncoder->encodePassword($user, $signUpRequest->getPassword()))
            ->setUsername($signUpRequest->getUsername());

        $encodedPassword = $this->passwordEncoder->hashPassword($user, $signUpRequest->getPassword());
        $user->setPassword($encodedPassword);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }
}
