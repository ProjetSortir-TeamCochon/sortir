<?php

namespace App\Repository;

use App\Entity\Etat;
use App\Entity\Sortie;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use function Symfony\Component\DependencyInjection\Loader\Configurator\param;

/**
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

    public function getOpenSorties(int $pageN, int $maxResults)
    {
        $query = $this->createQueryBuilder('s')
            ->join('s.etat', 'etat')
            ->andWhere('s.dateHeureDebut >= :today')
            ->setParameter('today', new \DateTime())
            ->andWhere('etat.libelle <> :created')
            ->setParameter('created', Etat::CREATED)
            ->addOrderBy('s.dateHeureDebut', 'ASC')
            ->setFirstResult(0 + ($pageN - 1) * $maxResults)
            ->setMaxResults($maxResults);
        return new Paginator($query, true);
    }

    public function getSorties(int $pageN, int $maxResults, array $params, ?int $userId){
        // First request
        if(sizeof($params) == 0) return $this->getOpenSorties($pageN, $maxResults);
        $query = $this->createQueryBuilder('s');

            // TODO appliquer les filtres en fonction de params

            // Campus:
            if(array_key_exists('campus', $params)){
                $query->join('s.campus', 'c')
                    ->andWhere('c.nom = :campus')
                    ->setParameter('campus', $params['campus']);
            }

            // Min Date
            if(array_key_exists('minDate', $params)){
                $query->andWhere('s.dateHeureDebut >= :minDate')
                    ->set('minDate', $params['minDate']);
            }
            // Max Date
            if(array_key_exists('maxDate', $params)){
                $query->andWhere('s.dateHeureDebut <= :maxDate')
                    ->set('maxDate', $params['maxDate']);
            }

            // Additionnal filters
            if($userId && array_key_exists('filters', $params) && sizeof($params['filters']) > 0){
                $filters = $params['filters'];

                $manager = !!array_search('manager', $filters);
                if($manager){
                    $query->join('s.organisateur', 'o')
                        ->andWhere('o.id = :userId')
                        ->setParameter('userId', $userId);
                }

                $registered = !!array_search('registered', $filters);
                $notRegistered = !!array_search('notRegistered', $filters);
                // if both do nothing
                if( ($registered || $notRegistered) && !($registered && $notRegistered) ){
                    $query->join('s.users', 'reg');
                    if($registered){
                        $query->andWhere('reg.id = :userId')
                            ->setParameter("userId", $userId);
                    }
                    if($notRegistered) {
                        $query->andWhere('reg.id != :userId')
                            ->setParameter("userId", $userId);
                    }
                }

                $past = !!array_search('past', $filters);
                if($past){
                    $query->andWhere('s.dateHeureDebut < :today')
                        ->setParameter('today', new \DateTime());
                }
            }

        // Pagination
        $query->setFirstResult(0 + ($pageN - 1) * $maxResults)
            ->setMaxResults($maxResults);
        return new Paginator($query, true);
    }
    // /**
    //  * @return Sortie[] Returns an array of Sortie objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Sortie
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
