<?php

namespace App\Controller;

use App\Entity\Order;
use App\Repository\OrderRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AccountOrderController extends AbstractController
{
    #[Route('/compte/mes-commandes', name: 'app_account_order')]
    public function index(OrderRepository $orderRepository): Response
    {
        return $this->render('account/order.html.twig', [
            'orders' => $orderRepository->findSuccessOrder($this->getUser()),
        ]);
    }

    #[Route(path: '/compte/mes-commandes/{reference}', name: 'app_account_order_show')]
    public function show(Order $order): Response
    {
        if (!$order || $order->getUser() != $this->getUser()) {
            return $this->redirectToRoute('app_account_order');
        }
        return $this->render('account/order_show.html.twig', [
            'order' => $order,
        ]);
    }

}
