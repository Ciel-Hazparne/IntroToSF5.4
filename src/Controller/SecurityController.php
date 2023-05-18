<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route('/registration', name: 'security_registration', methods: ['GET','POST'])]
    public function registration(Request $request, UserRepository $userRepository, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $hashedPassword = $passwordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($hashedPassword);
            $userRepository->save($user, true);
            return $this->redirectToRoute('security_login');
        }
        return $this->render('security/registration.html.twig',
            ['form' => $form->createView()]);
    }

    #[Route('/login', name: 'security_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // récupère les éventuelles erreur de connexion
        $error = $authenticationUtils->getLastAuthenticationError();
        // dernier nom entré par l'utilisateur
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('security/login.html.twig', ['lastUsername'=>$lastUsername,'error' => $error]);
    }

    #[Route('/logout', name: 'security_logout')]
    public function logout(): void
    {
    }
}
