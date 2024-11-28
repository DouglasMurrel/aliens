<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Component\Serializer\SerializerInterface;
use App\VK\Provider\User as VkUser;
use Doctrine\ORM\EntityManagerInterface;

class UserInfo {
    private $serializer;
    private $entityManager;
    
    public function __construct(
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer
    )
    {
        $this->entityManager = $entityManager;
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
    
    function getVkUser(VkUser $vkUser): User
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
    
    private function randomStr(
        int $length = 64,
        string $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'
    ): string {
        if ($length < 1) {
            throw new \RangeException("Length must be a positive integer");
        }
        $pieces = [];
        $max = mb_strlen($keyspace, '8bit') - 1;
        for ($i = 0; $i < $length; ++$i) {
            $pieces []= $keyspace[random_int(0, $max)];
        }
        return implode('', $pieces);
    }
}
