<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\OrderCan;
use App\Entity\OrderWant;
use App\Entity\OrderNoes;
use Symfony\Component\Serializer\SerializerInterface;
use App\VK\Provider\User as VkUser;
use App\Http\Request\SignUpRequest;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\UserCreator;

class UserInfo {
    private $serializer;
    private $entityManager;
    private $userCreator;
    
    public function __construct(
        EntityManagerInterface $entityManager,
        UserCreator $userCreator,
        SerializerInterface $serializer
    )
    {
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
        $this->userCreator = $userCreator;
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
                ->setFullname($vkUser->getFirstName().' '.$vkUser->getLastName())
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
    
    public function getOrdersHeplerCan(){
        $helper = $this->entityManager->getRepository(OrderCan::class)->findAll();
        $json = $this->serializer->serialize(
            $helper,
            'json', 
            ['groups' => 'userinfo']
        );
        return $json;
    }
    
    public function getOrdersHeplerWant(){
        $helper = $this->entityManager->getRepository(OrderWant::class)->findAll();
        $json = $this->serializer->serialize(
            $helper,
            'json', 
            ['groups' => 'userinfo']
        );
        return $json;
    }
    
    public function getOrdersHeplerNoes(){
        $helper = $this->entityManager->getRepository(OrderNoes::class)->findAll();
        $json = $this->serializer->serialize(
            $helper,
            'json', 
            ['groups' => 'userinfo']
        );
        return $json;
    }
}
