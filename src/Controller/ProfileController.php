<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Form\PasswordUpdateFormType;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Password\PasswordHasherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function index(Request $request, EntityManagerInterface $entityManager, PasswordHasherInterface $passwordHasher): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(PasswordUpdateFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $oldPassword = $form->get('oldpassword')->getData();
            $newPassword = $data->getPassword();

            // Vérifier que l'ancien mot de passe est correct
            if (!password_verify($oldPassword, $user->getPassword())) {
                
                // Si l'ancien mot de passe est incorrect, ajouter un message d'erreur
                $this->addFlash('error', 'L\'ancien mot de passe est incorrect.');
                return $this->redirectToRoute('app_profile');
            }

            // Encoder et mettre à jour le mot de passe
            // $encodedPassword = $passwordHasher->hash($newPassword);
            // $user->setPassword($encodedPassword);
            $encodedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

            $user->setPassword($newPassword);


            // Sauvegarder les modifications
            $entityManager->flush();

            // Message de succès
            $this->addFlash('success', 'Votre mot de passe a été mis à jour avec succès.');

            return $this->redirectToRoute('app_profile');
        }

        return $this->render('profile/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
