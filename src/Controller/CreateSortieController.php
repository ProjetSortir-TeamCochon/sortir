<?php


namespace App\Controller;


use App\Entity\Sortie;
use App\Entity\Lieu;
use App\Entity\Ville;
use App\Entity\Etat;
use App\Form\SortieType;
use App\Repository\SortieRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;



/**
 * @Route("/sortie", name="sortie_")
 */
class CreateSortieController extends AbstractController
{
    /**
     * @Route("/create", name="create")
     */
    public function create(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response
    {
        $sortie = new Sortie();
        $user = $this->getUser();
        $sortieForm = $this->createForm(SortieType::class, $sortie);

        $sortieForm->handleRequest($request);

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()){

            $sortie->setOrganisateur($user);
            $entityManager->persist($sortie);
            $entityManager->flush();

            $this->addFlash('succeess','La sortie a été créée');

            // redirection à changer vers affichage de la sortie
            return $this->redirectToRoute('main_accueil');
        }

        return $this->render('create/createSortie.html.twig', [
            'sortieForm' => $sortieForm->createView()
        ]);
    }


    /**
     * @Route("/modif/{id}", name="modif")
     */
    public function modifie(int $id, SortieRepository $sortieRepository,EntityManagerInterface $entityManager,Request $request)
    {
        $sortie = $sortieRepository->find($id);
        $user = $this->getUser();

        if($user != $sortie->getOrganisateur()){
            $this->addFlash('error',"N'est pas le bon utilisateur");
            return $this->redirectToRoute('main_accueil');
        }
        $sortieForm = $this->createForm(SortieType::class, $sortie);

        $sortieForm->handleRequest($request);

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()){

            $entityManager->persist($sortie);
            $entityManager->flush();

            $this->addFlash('succeess','La sortie a été modifiée');

            // redirection à changer vers affichage de la sortie
            return $this->redirectToRoute('main_accueil');
        }

        return $this->render('create/modifSortie.html.twig', [
            'sortieForm' => $sortieForm->createView()
        ]);

    }

}