<?php


namespace App\Controller;


use App\Entity\Sortie;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/sorties", name="sorties_")
 */
class SortiesController extends AbstractController
{

    /**
     * @Route("/details/{id}", name="details")
     */

    public function afficherSorties(int $id, SortieRepository $sortieRepository)

    {

        $sortie = $sortieRepository->find($id);



        return $this->render('sorties/afficherSorties.html.twig', [
            "sortie" => $sortie
        ]);
    }

    /**
     * @Route("/annuler/{id}", name="annuler")
     */
    public function annuler(int $id, SortieRepository $sortieRepository)
    {
        $sortie = $sortieRepository->find($id);

        return $this->render('sorties/annulerSorties.html.twig', [
            "sortie" => $sortie
        ]);
    }



    /**
     * @Route("/effacer/{id}", name="effacer")
     */
    public function effacerSorties(Sortie $sortie, EntityManagerInterface $entityManager)
    {
        $entityManager->remove($sortie);
        $entityManager->flush();

        return $this->redirectToRoute('main_accueil');

        return $this->render('sorties/effacerSorties.html.twig');
    }





}