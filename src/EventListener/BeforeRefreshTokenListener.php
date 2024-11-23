<?php

namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class BeforeRefreshTokenListener {
    private $entityManager;
    private $logger;
    
    public function __construct(
        EntityManagerInterface $entityManager,
        LoggerInterface $logger
    )
    {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

//костыль:( нужен потому, что где-то перед flush() пароль текущего пользователя в Doctrine устанавливается в null, а flush нужен для записи refresh_token
    public function beforeRefreshToken(AuthenticationSuccessEvent $event): void
    {
        $user = $event->getUser();
        $this->entityManager->refresh($user);
    }
}
