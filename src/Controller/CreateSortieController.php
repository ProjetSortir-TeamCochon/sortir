<?php


namespace App\Controller;


use App\Entity\Sortie;
use App\Form\SortieType;
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

        $sortieForm = $this->createForm(SortieType::class, $sortie);

        $sortieForm->handleRequest($request);

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()){

            $sortie->setEtat(1);

            $entityManager->persist($sortie);
            $entityManager->flush();

            $this->addFlash('succeess','La sortie a été créée');

            // redirection à changer vers affichage de la sortie
            return $this->redirectToRoute('sortie_create',['id' => $sortie->getId()]);
        }

        return $this->render('create/createSortie.html.twig', [
            'sortieForm' => $sortieForm->createView()
        ]);
    }

}