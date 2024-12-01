<?php

namespace App\EventListener;

use App\Event\NewOrderEvent;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Psr\Log\LoggerInterface;

class NewOrderMailListener {
    
    private $mailer;
    private $logger;    
    
    public function __construct(
        MailerInterface $mailer,
        LoggerInterface $logger
    )
    {
        $this->mailer = $mailer;
        $this->logger = $logger;
    }
    
    public function sendOrderEmail(NewOrderEvent $event)
    {
        $email = (new Email())
            ->from('murrel@yandex.ru')
            ->to('murrel@yandex.ru')
            ->subject('Заявка на игру')
            ->text("Дорогие мастера!\nВам пришла новая заявка, или была отредактирована существующая!\nВот она:\n" . $event->getOrder());

        $this->mailer->send($email);
    }
    
}
