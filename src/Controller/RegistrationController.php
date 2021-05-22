<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Form\UploadAvatarForm;
use App\Repository\UserRepository;
use App\Security\AppAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
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

            $this->addFlash('success', 'Un nouvel élève a été ajouté !');

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('main_accueil');
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
        if ($user !== $this->getUser()) {
            throw new AccessDeniedException("Vous n'avez pas l'accès à cette page !");
        }

        $form = $this->createForm(RegistrationFormType::class, $user);
        $uploadAvatarForm = $this->createForm(UploadAvatarForm::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

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

            return $this->redirectToRoute('profile', [
                'id' => $user->getId()
            ]);
        }

        return $this->render('profile/profile.html.twig', [
            'registrationForm' => $form->createView(),
            'uploadAvatarForm' => $uploadAvatarForm->createView()
        ]);

    }

    /**
     * @Route("profile/avatar/upload/{id}", name="profile_picture")
     */
    public function upload(Request $request, User $user, EntityManagerInterface $entityManager)
    {
        // S'il y a l'avatar stocké sur serveur, le supprime.
        if($user->getProfilePictureName()) {
            $profilePictureName = $user->getProfilePictureName();
            $filesystem = new Filesystem();
            $filesystem->remove('img/profile-picture/'.$profilePictureName);
        }

        $uploadedFile = $request->files->get('upload_avatar_form')['profilePictureName'];
        $destination = $this->getParameter('kernel.project_dir').'/public/img/profile-picture';
        $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
        $newFilename = $originalFilename.'-'.uniqid().'.'.$uploadedFile->guessExtension();
        $uploadedFile->move(
            $destination,
            $newFilename
        );
        $user->setProfilePictureName($newFilename);
        $entityManager->flush();

        return $this->redirectToRoute('profile', [
            'id' => $user->getId()
        ]);
    }

    /**
     * @Route ("/profile/fiche/{id}", name="fiche")
     */
    public function fiche(int $id, UserRepository $userRepository): Response
    {

        $user = $userRepository->find($id);

        if (!$user) {
            throw $this->createNotFoundException("Impossible d'afficher ce profil !");
        }

        return $this->render('profile/fiche.html.twig', [
            "user" => $user
        ]);
    }
}
