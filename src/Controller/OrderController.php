<?php

namespace App\Controller;

use App\Entity\Order;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class OrderController extends AbstractController
{
    #[Route('/orders', name: 'order_history')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $user = $this->getUser();
        $orders = [];

        if ($user) {
            $orders = $doctrine->getRepository(Order::class)->findBy(['user' => $user]);
        }

        return $this->render('order/order_history.html.twig', [
            'orders' => $orders
        ]);
    }
}
