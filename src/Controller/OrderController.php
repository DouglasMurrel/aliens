<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Order;
use App\Entity\OrderWant;
use App\Entity\OrderNoes;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;


/**
 * Description of OrderController
 *
 * @author murrel
 */
class OrderController extends AbstractController{
    
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
    
    #[Route('/order', name: 'order', methods: ['POST','OPTIONS'])]
    public function order(#[CurrentUser] ?User $user, Request $request): JsonResponse
    {
        if (null === $user) {
            return $this->json([
                'message' => 'missing credentials for userinfo',
            ], Response::HTTP_UNAUTHORIZED);
        }
        
        $data = json_decode($request->getContent(),true);
        
        $this->logger->info(print_r($data,1));
        $order = $user->getUserOrder();
        if ($order == null) {
            $order = new Order();
            $user->setUserOrder($order);
        }
        $order
            ->setMedical($this->getKey('medical', $data))
            ->setPsycological($this->getKey('psycological',$data))
            ->setFood($this->getKey('food',$data))
            ->setComment($this->getKey('comment',$data))
            ->setAdditional($this->getKey('additional',$data))
            ->setSchool($this->getKey('school',$data))
            ->setContact($this->getKey('contact',$data));
        foreach($order->getOrderWants() as $orderWant) {
            $order->removeOrderWant($orderWant);
        }
        if (isset($data['orderWants'])) {
            foreach($data['orderWants'] as $orderWant){
                $orderWantObject = $this->entityManager->getRepository(OrderWant::class)->find($orderWant['id']);
                if ($orderWantObject)$order->addOrderWant($orderWantObject);
            }
        }
        foreach($order->getOrderNoes() as $orderNo) {
            $order->removeOrderNo($orderNo);
        }
        if (isset($data['orderNoes'])) {
            foreach($data['orderNoes'] as $orderNoes){
                $orderNoesObject = $this->entityManager->getRepository(OrderNoes::class)->find($orderNoes['id']);
                if ($orderNoesObject)$order->addOrderNo($orderNoesObject);
            }
        }
        $this->entityManager->refresh($user);
        $this->entityManager->persist($order);
        $this->entityManager->flush();

        return $this->json($data);
    }
    
    private function getKey($key, $arr)
    {
        return isset($arr[$key])?$arr[$key]:'';
    }

}
