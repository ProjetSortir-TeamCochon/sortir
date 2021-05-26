<?php

namespace App\Controller;

use App\Data\SearchData;
use App\Entity\Ville;
use App\Form\GestionVilleType;
use App\Form\RechercherFormType;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/gestionville", name="gestionvilles_")
 */
class GestionVilleController extends AbstractController
{
    /**
     * @Route("/liste", name="liste")
     */
    public function list(VilleRepository $villeRepository,
                         Request $request,
                         EntityManagerInterface $entityManager)
    {
        $villes = $villeRepository->findAll();
        $newVille = new Ville();
        $data = new SearchData();

        $dataForm = $this->createForm(RechercherFormType::class);
        $villesForm = $this->createForm(GestionVilleType::class, $newVille);

        $villesForm->handleRequest($request);


        if($villesForm->isSubmitted())
        {
            $entityManager->persist($newVille);
            $entityManager->flush();

            $this->addFlash('success', 'Ville correctement ajoutée ');
            return $this->redirectToRoute('gestionvilles_liste');
        }

        return $this->render('gestion_ville/liste.html.twig', [
            "villes" => $villes,
            "villesForm" => $villesForm->createView(),
            "rechercherForm" => $dataForm->createView()
        ]);
    }


    /**
     * @Route("/modifier/{id}", name="modifier")
     */
    public function update(int $id,
                           VilleRepository $villeRepository,
                           Request $request,
                           EntityManagerInterface $entityManager)
    {

        $ville = $villeRepository->find($id);

        $dataForm = $this->createForm(RechercherFormType::class);
        $villeForm = $this->createForm(GestionVilleType::class, $ville);

        $villeForm->handleRequest($request);



        if($villeForm->isSubmitted())
        {
            $entityManager->persist($ville);
            $entityManager->flush();

            $this->addFlash('success', 'Ville correctement modifiée');
            return $this->redirectToRoute('gestionvilles_liste');
        }


        return $this->render('gestion_ville/modifier.html.twig', [
            "ville" => $ville,
            "villeForm" => $villeForm->createView(),
            "rechercherForm" => $dataForm->createView()
        ]);
    }

    /**
     * @Route("/effacer/{id}", name="effacer")
     */
    public function delete(Ville $ville,
                            EntityManagerInterface $entityManager)
    {
        $entityManager->remove($ville);
        $entityManager->flush();
        $this->addFlash('success', 'Ville correctement supprimée');
        return $this->redirectToRoute('gestionvilles_liste');

    }

}
