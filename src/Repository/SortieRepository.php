<?php

namespace App\Repository;

use App\Entity\Etat;
use App\Entity\Sortie;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\DriverManager;
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
            ->addOrderBy('s.dateHeureDebut', 'ASC');

        // Pagination
        $query->getQuery();
        $paginator = new Paginator($query);
        $paginator->getQuery()
            ->setFirstResult(($pageN - 1) * $maxResults)
            ->setMaxResults($maxResults);
        return $paginator;
    }

    public function getSorties(int $pageN, int $maxResults, array $params, ?int $userId){
        // First request
        if(sizeof($params) == 0) return $this->getOpenSorties($pageN, $maxResults);
        $query = $this->createQueryBuilder('s');
            $campus = array_key_exists('campus', $params) ? $params['campus'] : false;
            // Campus:
            if(!!$campus){
                $query->join('s.campus', 'c')
                    ->andWhere('c.nom = :campus')
                    ->setParameter('campus', $params['campus']->getNom());
            }

            // Min Date
            $minDate = array_key_exists('minDate', $params) ? $params['minDate'] : false;
            if(!!$minDate){
                $query->andWhere('s.dateHeureDebut >= :minDate')
                    ->setParameter('minDate', $params['minDate']);
            }
            // Max Date
            $maxDate = array_key_exists('maxDate', $params) ? $params['maxDate'] : false;
            if(!!$maxDate){
                $query->andWhere('s.dateHeureDebut <= :maxDate')
                    ->setParameter('maxDate', $params['maxDate']);
            }

            // Additionnal filters
            $filters = array_key_exists('filters', $params) ? $params['filters'] : false;
            $past = array_search('past', $filters) !== false;
            if($past){
                $query->andWhere('s.dateHeureDebut < :today')
                    ->setParameter('today', new \DateTime())
                    ->addOrderBy('s.dateHeureDebut', 'DESC');
            } else {
                $query->andWhere('s.dateHeureDebut >= :today')
                    ->setParameter('today', new \DateTime())
                    ->addOrderBy('s.dateHeureDebut', 'ASC');
            }
            if( !!$userId && $userId >= 0 && sizeof($filters) > 0){

                $manager = array_search('manager', $filters) !== false;
                if($manager){
                    $query->join('s.organisateur', 'o')
                        ->andWhere('o.id = :userId')
                        ->setParameter('userId', $userId);
                }

                $registered = array_search('registered', $filters) !== false;
                $notRegistered = array_search('notRegistered', $filters) !== false;
                // if both do nothing
                if( ($registered || $notRegistered) && !($registered && $notRegistered) ){
                    if($registered){
                        $query->join('s.users', 'reg');
                        $query->andWhere('reg.id = :userId')
                        ->setParameter("userId", $userId);
                    }
                    if($notRegistered) {
                        $queryBis = $this->createQueryBuilder('sort')
                                        ->join('s.users', 'reg')
                                        ->andWhere('reg.id = :userId');
                        $query->andWhere('s.id NOT in ('.$queryBis->getDQL().')')
                            ->setParameter("userId", $userId);
                    }
                }
            }

        // Pagination
        $query->getQuery();
        $paginator = new Paginator($query);
        $paginator->getQuery()
            ->setFirstResult(($pageN - 1) * $maxResults)
            ->setMaxResults($maxResults);
        return $paginator;
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
