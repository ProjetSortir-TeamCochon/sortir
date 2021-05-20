<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\AppAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;


class RegistrationController extends AbstractController
{
    /**
     * @Route("/admin/register", name="app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, AppAuthenticator $authenticator): Response
    {
        $user = new User();
        $user->setRoles(["ROLE_USER"]);
        $user->setAdministrateur(0);
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email
            // TODO : redirection après inscription d'un nouvel élève vers une autre page + message flash succès
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route ("/profile/{id}", name="profile")
     */
    public function editProfile(
        User $user,
        EntityManagerInterface $entityManager,
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder)
    {
        if ($user !== $this->getUser())
        {
            throw new AccessDeniedException("Oulala c pa bo de fer sa");
        }

        $form = $this->createForm(RegistrationFormType::class, $user);

        $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()){

                $user = $form->getData();
                $user->setPassword(
                    $passwordEncoder->encodePassword(
                        $user,
                        $form->get('password')->getData()
                    )
                );

                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash('success', 'Votre profil a été édité avec succès !');

                return $this->redirectToRoute('profile',[
                    'id' => $user->getId()
                ]);
            }

            return $this->render('profile/profile.html.twig',[
                'registrationForm' => $form->createView(),
            ]);

    }

    /**
     * @Route ("/profile/fiche/{id}", name="fiche")
     */
    public function fiche(int $id, UserRepository $userRepository):Response{

        $user = $userRepository->find($id);

        if (!$user) {
            throw $this->createNotFoundException("Impossible d'afficher ce profil !");
        }

        return $this->render('profile/fiche.html.twig',[
            "user" => $user
        ]);
    }
}
