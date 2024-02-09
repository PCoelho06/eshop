<?php

namespace App\Controller;

use App\Services\Mail;
use App\Entity\ResetPassword;
use App\Form\ResetPasswordType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ResetPasswordRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ResetPasswordController extends AbstractController
{
    #[Route('/mot-de-passe-oublie', name: 'app_reset_password')]
    public function index(Request $request, UserRepository $userRepository, EntityManagerInterface $manager, Mail $mail): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute("app_home");
        }

        if ($request->get('email')) {
            $user = $userRepository->findOneByEmail($request->get('email'));
            if ($user) {
                $reset_password = new ResetPassword();
                $reset_password->setUser($user)
                    ->setToken(uniqid())
                    ->setCreatedAt(new \DateTimeImmutable());
                $manager->persist($reset_password);
                $manager->flush();

                $url = $this->generateUrl('update_password', [
                    'token' => $reset_password->getToken(),
                ]);
                $content = '<a href="' . $_SERVER['HTTP_ORIGIN'] . $url . ' ">Cliquer pour réinitialiser</a>';
                $mail->send($user->getEmail(), $user->GetFullName(), 'Réinitialisation mot de passe', $content);
                $this->addFlash(
                    'info',
                    "Vous allez recevoir un email avec la procédure de réinitialisation du mot de passe"
                );
            } else {
                $this->addFlash(
                    'danger',
                    "Cette adresse email est inconnue"
                );
            }
        }

        return $this->render('reset_password/index.html.twig');
    }

    #[Route(path: '/modifier-mon-mot-de-passe/{token}', name: 'app_update_password')]
    public function update($token, UserPasswordHasherInterface $passwordHasher, ResetPasswordRepository $resetPasswordRepository, Request $request, EntityManagerInterface $manager): Response
    {
        $reset_password = $resetPasswordRepository->findOneByToken($token);

        if (!$reset_password) {
            return $this->redirectToRoute('app_reset_password');
        }

        $date_create = $reset_password->getCreatedAt();

        $now = new \DateTime();
        if ($now > $date_create->modify('+ 3 hour')) {
            $this->addFlash(
                'danger',
                "Votre demande de modification de mot de passe a expiré"
            );
            return $this->redirectToRoute('app_reset_password');
        }

        $user = $reset_password->getUser();

        $form = $this->createForm(ResetPasswordType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($passwordHasher->hashPassword(
                $user,
                $user->getNewPassword()
            ));
            
            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                "Le nouveau mot de passe a bien été créé"
            );
            return $this->redirectToRoute('app_login');
        }

        return $this->render('reset_password/update.html.twig', [
            "form" => $form->createView(),
        ]);
    }
}
