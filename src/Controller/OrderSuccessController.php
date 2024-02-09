<?php

namespace App\Controller;

use App\Entity\Order;
use App\Services\Cart;
use App\Services\Mail;
use Stripe\StripeClient;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderSuccessController extends AbstractController
{
    #[Route('/commande/succes/{stripeSessionId}', name: 'app_order_success')]
    public function index(Order $order, EntityManagerInterface $manager, Cart $cart, $stripeSessionId, Mail $mail): Response
    {
        if (!$order || $order->getUser() != $this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        $stripe = new StripeClient('sk_test_51OhU7TCyaLq5pcktoQdgoMrJA9ONYVMc0jiImc9XuZNBFECuuk14dtrz76cQoSKmioZIM0c3JWhf9boqjtuFEoTK007eTqX3xD');
        $session = $stripe->checkout->sessions->retrieve($stripeSessionId);

        if ($session->payment_status != "paid") {
            return $this->redirectToRoute('app_order_cancel', ['stripeSessionId' => $stripeSessionId]);
        }

        if (!$order->getStatut()) {
            $cart->remove();
            $order->setStatut(1);
            $manager->flush();
        }

        $content = 'Merci pour votre commande' . $order->getUser()->GetFullName() . 'bla bla bla';
        $mail->send($order->getUser()->getEmail(), $order->getUser()->GetFullName(), 'Votre commande est bien validée', $content);

        return $this->render('order_success/index.html.twig', [
            'order' => $order,
        ]);
    }
}
