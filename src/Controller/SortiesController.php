<?php


namespace App\Controller;

use App\Entity\Sortie;
use App\Form\AnnulerSortieType;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
    public function annuler(int $id,
                            SortieRepository $sortieRepository,
                            EtatRepository $etatRepository,
                            Request $request,
                            EntityManagerInterface $entityManager)
    {
        $sortie = $sortieRepository->find($id);
        $motifForm = $this->createForm(AnnulerSortieType::class);

        $motifForm->handleRequest($request);

        if ($motifForm->isSubmitted())
        {
            $etat = $etatRepository->find(2);
            $sortie->setEtat($etat);
            $entityManager->persist($sortie);
            $entityManager->flush();

            $this->addFlash('success', 'Sortie annulée');
            return $this->redirectToRoute('main_accueil');
        }

        return $this->render('sorties/annulerSorties.html.twig', [
            "sortie" => $sortie,
            "motifForm" => $motifForm->createView()
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