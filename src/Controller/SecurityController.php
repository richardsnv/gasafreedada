<?php

namespace App\Controller;

use App\Form\PasswordFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route(path:'password/{token}', name:'verify_user',methods:['GET','POST'])]
    public function authenticator(AuthenticationUtils $authenticationUtils,string $token,Request $request,UserRepository $userRepository,EntityManagerInterface $manager): Response
    {
        $user=$userRepository->findOneBy(['token'=> $token]);
        if (null === $user) {

            return $this->redirectToRoute('verifay.user');
        } 

        $form= $this->createForm(PasswordFormType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(password_hash($form->getData()['password'], PASSWORD_DEFAULT));
            $manager->flush();
        }
        return $this->render('security/password.html.twig', [
            'form'=>$form
        ]);
    }
}
