<?php

namespace App\Controller;

use App\Data\SearchData;
use App\Entity\Campus;
use App\Form\GestionCampusFormType;
use App\Form\RechercherFormType;
use App\Repository\CampusRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/gestioncampus", name="gestioncampus_")
 */
class GestionCampusController extends AbstractController
{
    /**
     * @Route("/liste", name="liste")
     */
    public function list(CampusRepository $campusRepository,
                         Request $request,
                         EntityManagerInterface $entityManager)
    {

        $campus = $campusRepository->findAll();
        $newCampus = new Campus();
        $data = new SearchData();

        $dataForm = $this->createForm(RechercherFormType::class);
        $campusForm = $this->createForm(GestionCampusFormType::class, $newCampus);

        $campusForm->handleRequest($request);

        if($campusForm->isSubmitted() && $campusForm->isValid())
        {
            $entityManager->persist($newCampus);
            $entityManager->flush();

            $this->addFlash('success', 'Campus correctement ajouté !');
            return $this->redirectToRoute('gestioncampus_liste');
        }
        if($campusForm->isSubmitted() && !$campusForm->isValid())
        {
        $this->addFlash('danger', 'Campus non ajoutée');
        return $this->redirectToRoute('gestioncampus_liste');
         }


        return $this->render('gestion_campus/liste.html.twig', [
            "campus" => $campus,
            "campusForm" => $campusForm->createView(),
            "rechercherForm" => $dataForm->createView()
        ]);
    }

    /**
     * @Route("/modifier/{id}", name="modifier")
     */
    public function update(int $id, CampusRepository $campusRepository, Request $request, EntityManagerInterface $entityManager)
    {
        $campus = $campusRepository->find($id);

        $campusForm = $this->createForm(GestionCampusFormType::class, $campus);
        $dataForm = $this->createForm(RechercherFormType::class);
        $campusForm->handleRequest($request);

        if($campusForm->isSubmitted() && $campusForm->isValid())
        {
            $entityManager->persist($campus);
            $entityManager->flush();

            $this->addFlash('success', 'Campus correctement modifié !');
            return $this->redirectToRoute('gestioncampus_liste');
        }
        if($campusForm->isSubmitted() && !$campusForm->isValid())
        {
            $this->addFlash('danger', 'Campus non ajoutée');
            return $this->redirectToRoute('gestioncampus_modifier', ['id'=>$id]);
        }

        return $this->render('gestion_campus/modifier.html.twig', [
            "campus" => $campus,
            "campusForm" => $campusForm->createView(),
            "rechercherForm" => $dataForm->createView()
        ]);

    }

    /**
     * @Route("/effacer/{id}", name="effacer")
     */
    public function delete(Campus $campus, EntityManagerInterface $entityManager)
    {
        $dataForm = $this->createForm(RechercherFormType::class);
        $entityManager->remove($campus);
        $entityManager->flush();
        $this->addFlash('success', 'Campus correctement supprimé');
        return $this->redirectToRoute('gestioncampus_liste');

    }

}
