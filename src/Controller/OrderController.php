<?php

namespace App\Controller;

use Stripe\Stripe;
use App\Entity\Order;
use App\Services\Cart;
use App\Form\OrderType;
use App\Entity\OrderDetails;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderController extends AbstractController
{
    #[Route('/commande', name: 'app_order')]
    public function index(Request $request, EntityManagerInterface $manager, Cart $cart, ProductRepository $productRepository): Response
    {
        if (!$this->getUser()->getAddresses()->getValues()) {
            return $this->redirectToRoute('app_account_address_add');
        }

        $cart = $cart->get();
        $cartComplete = [];
        foreach ($cart as $id => $quantity) {
            $cartComplete[] = [
                'product' => $productRepository->findOneById($id),
                'quantity' => $quantity,
            ];
        }

        $form = $this->createForm(OrderType::class, null, [
            'user' => $this->getUser(),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $order = new Order();

            $order->setUser($this->getUser())
                ->setCreatedAt(new \DateTimeImmutable())
                ->setCarrier($form->get('transporteurs')->getData())
                ->setDelivery($form->get('addresses')->getData())
                ->setStatut(0);

            $date = new \DateTime();
            $date = $date->format('dmY');

            $order->setReference($date . '-' . uniqid());

            $manager->persist($order);

            foreach ($cartComplete as $product) {
                $orderDetails = new OrderDetails();

                $orderDetails->setMyOrder($order)
                    ->setProduct($product['product'])
                    ->setQuantity($product['quantity'])
                    ->setPrice($product['product']->getPrice());

                $product_for_stripe[] = [
                    'price_data' => [
                        'currency' => 'eur',
                        'product_data' => [
                            'name' => $product['product']->getName(),
                            'images' => [
                                $_SERVER['HTTP_ORIGIN'] . '/uploads/' . $product['product']->getIllustration(),
                            ]
                        ],
                        'unit_amount' => $product['product']->getPrice(),
                    ],
                    'quantity' => $product['quantity'],
                ];


                $manager->persist($orderDetails);
            }

            $product_for_stripe[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => $order->getCarrier()->getName(),
                    ],
                    'unit_amount' => $order->getCarrier()->getPrice(),
                ],
                'quantity' => 1,
            ];

            Stripe::setApiKey('sk_test_51OhU7TCyaLq5pcktoQdgoMrJA9ONYVMc0jiImc9XuZNBFECuuk14dtrz76cQoSKmioZIM0c3JWhf9boqjtuFEoTK007eTqX3xD');
            $YOUR_DOMAIN = 'http://localhost:8000';
            $checkout_session = \Stripe\Checkout\Session::create([
                'line_items' => $product_for_stripe,
                'mode' => 'payment',
                'customer_email' => $this->getUser()->getEmail(),
                'success_url' => $YOUR_DOMAIN . '/commande/succes/{CHECKOUT_SESSION_ID}',
                'cancel_url' => $YOUR_DOMAIN . '/commande/echec/{CHECKOUT_SESSION_ID}',
            ]);

            $order->setStripeSessionId($checkout_session->id);

            $manager->flush();

            return $this->render('order/recap.html.twig', [
                'cart' => $cartComplete,
                'order' => $order,
                'stripe_checkout_session' => $checkout_session->url,
            ]);
        }

        return $this->render('order/index.html.twig', [
            'form' => $form->createView(),
            'cart' => $cartComplete,
        ]);
    }
}
