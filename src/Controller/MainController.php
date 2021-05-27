<?php


namespace App\Controller;

use App\Form\SearchFiltersType;
use App\Repository\SortieRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use function Symfony\Component\DependencyInjection\Loader\Configurator\param;

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

        if(!$params) {
            $params = array();
        } else {
            // ------------- Date Validation

            // Min Date
            $minDate = array_key_exists('minDate', $params) ? $params['minDate'] : false;
            // Max Date
            $maxDate = array_key_exists('maxDate', $params) ? $params['maxDate'] : false;

            // Date Filtering and Validation
            if(!!$maxDate || !!$minDate){
                if(!!$minDate && !!$maxDate && $minDate > $maxDate){
                    $temp = $minDate;
                    $minDate = $maxDate;
                    $maxDate = $temp;
                }

                $today = new DateTime();
                $aMonth = (new DateTime())->sub(new \DateInterval("P1M"));

                if(!$minDate && $maxDate > $today){
                    $minDate = $today;
                } else if(!$minDate && $maxDate < $today){
                    $minDate = $aMonth;
                }
                if(!!$minDate){
                    // Default one month before today
                    $minDate = $minDate < $aMonth ? $aMonth : $minDate;
                }

                if(!!$minDate) {
                    $params['minDate'] = $minDate;
                }
                if(!!$maxDate) {
                    $params['maxDate'] = $maxDate;
                }
            }
        }

        $paginator = $sortieRepository->getSorties(
            $page,
            $maxResults,
            $params,
            $user ? $user->getId() : null
        );

        return $this->render('main/home.html.twig', [
            'page' => $page,
            'maxResults' => $maxResults,
            'maxPages' => ceil($paginator->count() / $maxResults),
            'sorties' => $paginator,
            'searchForm' => $searchForm->createView(),
            'params' => $params
        ]);
    }
}

