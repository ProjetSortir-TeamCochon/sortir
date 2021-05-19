<?php


namespace App\Controller;


use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/sorties", name="sorties_")
 */
class OutputsController extends AbstractController
{

    /**
     * @Route("/details/{id}", name="details")
     */

    public function showOutputs(int $id, SortieRepository $sortieRepository)

    {

        $output = $sortieRepository->find($id);

        return $this->render('outputs/showOutputs.html.twig', [
            "output" => $output
        ]);
    }
}