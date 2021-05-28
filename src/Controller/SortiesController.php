<?php


namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Sortie;
use App\Entity\User;
use App\Form\AnnulerSortieType;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/sorties", name="sorties_")
 * @IsGranted("ROLE_USER")
 */
class SortiesController extends AbstractController
{

    /**
     * @Route("/details/{id}", name="details")
     */

    public function afficherSorties(int $id,
                                    UserRepository $userRepository,
                                    SortieRepository $sortieRepository)

    {

        $sortie = $sortieRepository->find($id);

        $usersRegistered = $sortie->getUsers();



        return $this->render('sorties/afficherSorties.html.twig', [
            "sortie" => $sortie,
            "usersRegistered" => $usersRegistered
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
        $etat = $etatRepository->find(6);
        $sortie->setEtat($etat);
        $motifForm->handleRequest($request);

        if ($motifForm->isSubmitted()) {

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
    }

    /**
     * @Route("/inscription/{id}", name="inscription")
     */
    public function inscription(int $id,
                                UserRepository $userRepository,
                                SortieRepository $sortieRepository,
                                Request $request,
                                EntityManagerInterface $entityManager)
    {

        $userActual = $this->getUser();
        $user = $userRepository->find($userActual);
        $sortie = $sortieRepository->find($id);

        $user->addSortie($sortie);
        $sortie->addUser($user);

        $entityManager->persist($user);
        $entityManager->persist($sortie);
        $entityManager->flush();
        $this->addFlash('success', 'Inscription validée');
        return $this->redirectToRoute('main_accueil');

    }

    /**
     * @Route("/desistement/{id}", name="desistement")
     */
    public function desistement(int $id,
                                UserRepository $userRepository,
                                SortieRepository $sortieRepository,
                                Request $request,
                                EntityManagerInterface $entityManager)
    {

        $userActual = $this->getUser();
        $user = $userRepository->find($userActual);
        $sortie = $sortieRepository->find($id);

        $user->removeSortie($sortie);
        $sortie->removeUser($user);

        $entityManager->persist($user);
        $entityManager->persist($sortie);
        $entityManager->flush();
        $this->addFlash('success', 'Desistement validé');
        return $this->redirectToRoute('main_accueil');
    }


    /**
     * @Route("/publier/{id}", name ="publier")
     */
    public function publier(int $id,
                            SortieRepository $sortieRepository,
                            EtatRepository $etatRepository,
                            Request $request,
                            EntityManagerInterface $manager)
    {
        $user = $this->getUser();
        $sortie = $sortieRepository->find($id);

        if($sortie->getEtat()->getLibelle() != Etat::CREATED){
            $this->addFlash('error', 'Sortie déjà publiée ou annulée.');
            return $this->redirectToRoute('main_accueil');
        }

        if($user != $sortie->getOrganisateur() && !$this->isGranted("ROLE_ADMIN") ){
            $this->addFlash('error', 'Vous n\' avez pas les droits sur cette sortie.');
            return $this->redirectToRoute('main_accueil');
        }

        $sortie->setEtat($etatRepository->findOneByLibelle(Etat::OPEN));
        $manager->persist($sortie);
        $manager->flush();
        $this->addFlash('success', "La sortie est publiée.");
        return $this->redirectToRoute('sorties_details', [ 'id' => $id ]);
    }
}