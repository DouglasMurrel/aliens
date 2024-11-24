<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Component\Serializer\SerializerInterface;

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
}
