<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Order;
use App\Service\OrderInfo;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

#[IsGranted('ROLE_ADMIN', statusCode: 404)]
#[Route('/admin', name: 'admin-', methods: ['POST','OPTIONS'])]
class AdminController extends AbstractController
{
    private $entityManager;
    private $logger;
    private $orderInfo;
    
    public function __construct(
        EntityManagerInterface $entityManager,
        OrderInfo $orderInfo,
        LoggerInterface $logger
    )
    {
        $this->orderInfo = $orderInfo;
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }
    
    #[Route('/all-orders', name: 'all-orders', methods: ['POST','OPTIONS'])]
    public function allOrders(#[CurrentUser] ?User $user, Request $request): JsonResponse
    {
        $orders = $this->entityManager->getRepository(Order::class)->findAllOrders();
        return $this->json($this->orderInfo->getOrdersInfo($orders));
    }
}
