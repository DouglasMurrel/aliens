<?php

namespace App\Service;

use Psr\Log\LoggerInterface;

class SHA256Helper {
    private $logger;

    public function __construct(
            LoggerInterface $logger
    )
    {
        $this->logger = $logger;
    }
    
    public function SHA256(?string $s): string
    {
        $s = (string)$s;
        $sha = hash('sha256', $s, true);
        $this->logger->info("sha: ".$sha);
        $bse = base64_encode($sha);
        $this->logger->info("bse: ".$sha);
        return rtrim(strtr($bse, '+/', '-_'), '=');
    }
}
