<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Component\Serializer\SerializerInterface;
use KnpU\OAuth2ClientBundle\Client\Provider\VKontakteClient;

class UserInfo {
    private $serializer;
    
    public function __construct(
        SerializerInterface $serializer
    )
    {
        $this->serializer = $serializer;
    }
    
    public function getUserInfo(User $user) 
    {
        $json = $this->serializer->serialize(
            $user,
            'json', 
            ['groups' => 'userinfo']
        );
        return $json;
    }
    
    function getVkUser(VKontakteClient $vkUser): User
    {
        $email = $vkUser->getEmail();

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

        if (!$user) {
            $signUpRequest = new SignUpRequest();
            $signUpRequest
                ->setEmail($email)
                ->setFullname($user->getFirstName().' '.$user->getLastName())
                ->setPassword($this->randomStr())
            ;
            $user = $this->userCreator->createUser($signUpRequest);
            $this->entityManager->persist($user);
            $this->entityManager->flush($user);
        }
        
        return $user;
    }
}
