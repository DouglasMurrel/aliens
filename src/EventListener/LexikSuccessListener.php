<?php

namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;

class LexikSuccessListener {
    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event): void
    {
        $user = $event->getUser();
        $responseData = $event->getData();
        $responseData['user'] = $user->getUserIdentifier();
        $event->setData($responseData);
    }
}
