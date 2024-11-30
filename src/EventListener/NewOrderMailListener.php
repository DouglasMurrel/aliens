<?php

namespace App\EventListener;

use Psr\Log\LoggerInterface;

class NewOrderMailListener {
    
    private $logger;    
    
    public function __construct(
        LoggerInterface $logger
    )
    {
        $this->logger = $logger;
    }
    
    public function sendOrderEmail()
    {
        $this->logger->info('mail now');
    }
    
}
