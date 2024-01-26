<?php

namespace App\Controller;

use App\Form\ChangePasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AccountPasswordController extends AbstractController
{
    #[Route('/mon-compte/modifier-mon-mot-de-passe', name: 'app_account_password')]
    public function index(Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(ChangePasswordType::class, $user);

        $form->handleRequest($request);

        dump($user);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$passwordHasher->isPasswordValid($user, $user->getOldPassword())) {
                $this->addFlash('danger',"L'ancien mot de passe est incorrect");
            } else {
                $newPassword = $user->getNewPassword();
                $pass_encoded = $passwordHasher->hashPassword($user, $newPassword);
                $user->setPassword($pass_encoded);

                $manager->persist($user);
                $manager->flush();

                $this->addFlash('success',"Votre mot de passe a bien été modifié");

                return $this->redirectToRoute('app_profile');
            }
        }

        return $this->render('account_password/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
