<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use App\Services\Cart;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CartController extends AbstractController
{
    #[Route('/mon-panier', name: 'app_cart')]
    public function index(Cart $cart, ProductRepository $productRepository): Response
    {
        $cart = $cart->get();
        $completeCart = [];
        foreach ($cart as $id => $quantity) {
            $completeCart[] = [
            'product' => $productRepository->findOneById($id),
            'quantity' => $quantity
            ];
        }
        return $this->render('cart/index.html.twig', [
            'cart' => $completeCart,
        ]);
    }

    #[Route('/mon-panier/ajouter/{id}', name: 'app_cart_add')]
    public function add($id, Cart $cart): Response
    {
        $cart->add($id);

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/mon-panier/supprimer', name: 'app_cart_remove')]
    public function remove($id, Cart $cart): Response
    {
        $cart->remove();

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/mon-panier/supprimer-produit/{id}', name: 'app_cart_delete')]
    public function delete($id, Cart $cart): Response
    {
        $cart->delete($id);

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/mon-panier/reduire-produit/{id}', name: 'app_cart_decrease')]
    public function decrease($id, Cart $cart): Response
    {
        $cart->decrease($id);

        return $this->redirectToRoute('app_cart');
    }


}
