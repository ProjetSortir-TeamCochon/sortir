<?php


namespace App\Controller;


use App\Entity\Sortie;
use App\Form\SearchFiltersType;
use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="main_accueil")
     */
    public function home(Request $request,SortieRepository $sortieRepository)
    {
        $page = max(1, $request->query->getInt('page', 1));
        $maxResults = max(0, $request->query->getInt('maxResults', 20));

        $user = $this->getUser();

        $searchForm = $this->createForm(SearchFiltersType::class);
        $searchForm->handleRequest($request);

        if($searchForm->isSubmitted() && $searchForm->isValid()){
            $params = $searchForm->getData();
        } else {
            $params = $request->query->get('params');
        }
        if(!$params) $params = array();

        $paginator = $sortieRepository->getSorties(
            $page,
            $maxResults,
            $params,
            $user ? $user->getId() : null
        );

        return $this->render('main/home.html.twig', [
            'page' => $page,
            'maxResults' => $maxResults,
            'sorties' => $paginator,
            'searchForm' => $searchForm->createView(),
            'params' => $params
        ]);
    }
}

