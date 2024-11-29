<?php

namespace App\EventListener;

use App\Service\UserInfo;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;

class LexikSuccessListener {
    
    private $userInfo;
    
    public function __construct(
        UserInfo $userInfo
    )
    {
        $this->userInfo = $userInfo;
    }
   
    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event): void
    {
        $user = $event->getUser();
        $responseData = $event->getData();
        $responseData['user'] = $user->getUserIdentifier();
        $responseData['userData'] = $this->userInfo->getUserInfo($user);

        $hellers = [];
        $helpers['orderCan'] = $this->userInfo->getOrdersHeplerCan();
        $helpers['orderWant'] = $this->userInfo->getOrdersHeplerWant();
        $helpers['orderNoes'] = $this->userInfo->getOrdersHeplerNoes();
        $responseData['helpers'] = $helpers;
        
        
        $event->setData($responseData);
    }
}
