<?php

namespace App\Event;

use App\Entity\Order;
use Symfony\Contracts\EventDispatcher\Event;

class NewOrderEvent extends Event
{
    protected $order;
    
    public function __construct(Order $order)
    {
        $this->order = $order;
    }
    
    public function getOrder() {
        return $this->order;
    }

    public function setOrder($order): self 
    {
        $this->order = $order;
        return $this;
    }
}