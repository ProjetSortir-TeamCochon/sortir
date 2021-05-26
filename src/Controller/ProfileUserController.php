<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Form\UploadAvatarForm;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;


/**
 * @Route("/profile", name="profile_")
 */
class ProfileUserController extends AbstractController
{
    /**
     * @Route ("/edit/{id}", name="edit")
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

            return $this->redirectToRoute('profile_edit', [
                'id' => $user->getId()
            ]);
        }

        return $this->render('profile/profile.html.twig', [
            'registrationForm' => $form->createView(),
            'uploadAvatarForm' => $uploadAvatarForm->createView()
        ]);

    }

    /**
     * @Route("/avatar/upload/{id}", name="edit_avatar")
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

        return $this->redirectToRoute('profile_edit', [
            'id' => $user->getId()
        ]);
    }

    /**
     * @Route ("/user/{id}", name="user")
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
