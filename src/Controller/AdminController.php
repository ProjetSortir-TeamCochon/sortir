<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin", name="admin_")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/dashboard", name="dashboard")
     */
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    /**
     * @Route("/list", name="list")
     */
    public function list(UserRepository $userRepository){
        $users = $userRepository->findAll();

        return $this->render('admin/list.html.twig',[
            "users" => $users
        ]);
    }

    /**
     * @Route ("/delete/{id}", name="delete")
     */
    public function delete(
            int $id,
            EntityManagerInterface $entityManager
        ):Response{

        $user = $entityManager->getRepository(User::class)->find($id);

        $entityManager->remove($user);
        $entityManager->flush();

        $this->addFlash('success', 'L\'utilisateur a été supprimé avec succès !');

        return $this->render('admin/index.html.twig',[
            'id' => $user->getId()
        ]);
    }

    /**
     * @Route ("/activate/{id}", name="activate")
     */
    public function activate(
        int $id,
        EntityManagerInterface $entityManager
    ):Response{
        $user = $entityManager->getRepository(User::class)->find($id);
        $user->setActif(1);
        $entityManager->flush();

        $this->addFlash('success', 'L\'utilisateur a été modifié avec succès !');

        return $this->render('admin/index.html.twig',[
            'id' => $user->getId()
        ]);
    }

    /**
     * @Route ("/desactivate/{id}", name="desactivate")
     */
    public function desactivate(
        int $id,
        EntityManagerInterface $entityManager
    ):Response{
        $user = $entityManager->getRepository(User::class)->find($id);
        $user->setActif(0);
        $entityManager->flush();

        $this->addFlash('success', 'L\'utilisateur a été modifié avec succès !');

        return $this->render('admin/index.html.twig',[
            'id' => $user->getId()
        ]);
    }
}
