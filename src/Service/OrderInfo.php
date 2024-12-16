<?php

namespace App\Service;

use Symfony\Component\Serializer\SerializerInterface;

/**
 * Description of OrderInfo
 *
 * @author murrel
 */
class OrderInfo {
    private $serializer;

    public function __construct(
        SerializerInterface $serializer
    )
    {
        $this->serializer = $serializer;
    }
    
    public function getOrdersInfo($orders) 
    {
        $json = $this->serializer->serialize(
            $orders,
            'json', 
            ['groups' => 'userinfo']
        );
        return $json;
    }
}
