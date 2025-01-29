<?php

namespace App\Controller;

use App\Entity\User;
use App\Security\EmailVerifier;
use Symfony\Component\Mime\Email;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\UserAuthenticator;
use Symfony\Component\Mime\Address;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    public function __construct(private EmailVerifier $emailVerifier)
    {
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request,MailerInterface $mailer, UserPasswordHasherInterface $userPasswordHasher, Security $security, EntityManagerInterface $entityManager,UrlGeneratorInterface $urlGenerator): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            // $plainPassword = $form->get('plainPassword')->getData();

            // encode the plain password
            //  $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));
            $token = bin2hex(random_bytes(16)); // Génère un token de 32 caractères
            $user->setToken($token);
            $entityManager->persist($user);
            $entityManager->flush();

            $link = $urlGenerator->generate(
                'verify_user', // Nom de la route définie plus haut
                ['token' => $token],       // Paramètre à passer dans la route
                UrlGeneratorInterface::ABSOLUTE_URL // Générer un lien absolu
            );

            // Créer l'email
             $email = (new Email())
             ->from('maruisounouvou@gmail.com')  // Remplace par ton adresse email
             ->to($user->getEmail())  // Destinataire
             ->subject('test')  // Sujet de l'email
             ->text($link);  // Contenu de l'email en texte brut

            // // Envoi de l'email
             $mailer->send($email);
            // do anything else you need here, like send an email

            // return $security->login($user, UserAuthenticator::class);
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator, UserRepository $userRepository): Response
    {
        $id = $request->query->get('id');

        if (null === $id) {
            return $this->redirectToRoute('app_register');
        }

        $user = $userRepository->find($id);

        if (null === $user) {
            return $this->redirectToRoute('app_register');
        }

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            return $this->redirectToRoute('app_register');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('app_register');
    }
}
