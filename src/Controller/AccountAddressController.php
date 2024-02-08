<?php

namespace App\Controller;

use App\Services\Cart;
use App\Entity\Address;
use App\Form\AddressType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AccountAddressController extends AbstractController
{
    #[Route('/mon-compte/adresses', name: 'app_account_address')]
    public function index(): Response
    {
        return $this->render('account_address/index.html.twig', [
            'controller_name' => 'AccountAddressController',
        ]);
    }

    #[Route('/mon-compte/ajouter-une-adresse', name: 'app_account_address_add')]
    public function add(Request $request, EntityManagerInterface $manager, Cart $cart): Response
    {
        $address = new Address();

        $form = $this->createForm(AddressType::class, $address);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $address->setUser($this->getUser());

            $manager->persist($address);
            $manager->flush();

            $this->addFlash(
                'success',
                "L'adresse {$address->getName()} a bien été créé"
            );

            if ($cart->get()) {
                return $this->redirectToRoute('app_order');
            } else {
                return $this->redirectToRoute('app_account_address');
            }
        }

        return $this->render('account_address/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/mon-compte/modifier-une-adresse/{id}', name: 'app_account_address_edit')]
    public function edit(Request $request, EntityManagerInterface $manager, Address $address): Response
    {
        if (!$address || $address->getUser() != $this->getUser()) {
            return $this->redirectToRoute('app_account_address');
        }


        $form = $this->createForm(AddressType::class, $address);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->flush();

            $this->addFlash(
                'success',
                "L'adresse {$address->getName()} a bien été modifiée"
            );

            return $this->redirectToRoute('app_account_address');
        }

        return $this->render('account_address/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route(path: '/compte/supprimer-une-adresse/{id}', name: 'app_account_address_delete')]
    public function delete(EntityManagerInterface $manager, Address $address): Response
    {
        if ($address && $address->getUser() == $this->getUser()) {
            $manager->remove($address);
            $manager->flush();
            $this->addFlash(
                'success',
                "L'adresse {$address->getName()} a bien été supprimée"
            );
        }

        return $this->redirectToRoute('app_account_address');
    }
}
