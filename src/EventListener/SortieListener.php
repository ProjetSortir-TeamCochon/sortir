<?php


namespace App\EventListener;


use App\Entity\Etat;
use App\Entity\Sortie;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class SortieListener
{

    public $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function prePersist(Sortie $sortie, LifecycleEventArgs $event): void
    {
        $etat = $this->entityManager
            ->getRepository(Etat::class)->findOneByLibelle(Etat::CREATED);
        $sortie->setEtat($etat);
    }

    public function postLoad(Sortie $sortie, LifecycleEventArgs $event): void
    {
        $current = $sortie->getEtat()->getLibelle();

        if($current != Etat::CANCELLED || $current != Etat::CREATED){

            $today = new \DateTime();
            $etat = null;

            if($today <= $sortie->getDateLimiteInscription()) {
                if($current != Etat::OPEN) {
                    $etat = $this->entityManager
                        ->getRepository(Etat::class)->findOneByLibelle(Etat::OPEN);
                }
            }
            else if($today <= $sortie->getDateHeureDebut()) {
                if($current != Etat::CLOSED) {
                    $etat = $this->entityManager
                    ->getRepository(Etat::class)->findOneByLibelle(Etat::CLOSED);
                }
            }
            else {
                $duration = new \DateInterval("PT".$sortie->getDuree()."M");
                $end = (new \DateTime($sortie->getDateHeureDebut()->format(DATE_ISO8601)))->add($duration);
                if($today <= $end) {
                    if($current != Etat::RUNNING) {
                        $etat = $this->entityManager
                        ->getRepository(Etat::class)->findOneByLibelle(Etat::RUNNING);
                    }
                }
                else{
                    if($current != Etat::DONE) {
                        $etat = $this->entityManager
                        ->getRepository(Etat::class)->findOneByLibelle(Etat::DONE);
                    }
                }
            }

            if($etat) $sortie->setEtat($etat);
        }

    }

}