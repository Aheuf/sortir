<?php

namespace App\Repository;

use App\Entity\Participant;
use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Security;
use function Doctrine\ORM\QueryBuilder;


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

    // /**
    //  * @return Sortie[] Returns an array of Sortie objects
    //  */

    public function findByResearch($sortiesData, $security)
    {
        $queryBuilder = $this->createQueryBuilder('s');

        $queryBuilder->where('s.dateLimiteInscription > :dateLimite');
        $queryBuilder->setParameter('dateLimite', date('Y-m-d H:i:s', strtotime('-1 month')));

        //filtre choix du campus
        if ($sortiesData['campus'] != null) {
            $queryBuilder->leftJoin('s.campus', 'campus');
            $queryBuilder->andWhere('campus.nom = :campusNom');
            $queryBuilder->setParameter('campusNom', $sortiesData['campus']->getNom());
        }

        //filtre pour afficher les sorties entre deux dates
        if ($sortiesData['debut'] != null && $sortiesData['fin'] != null) {
            $queryBuilder->andWhere('s.dateHeureDebut >= :debut');
            $queryBuilder->setParameter('debut', $sortiesData['debut']);
            $queryBuilder->andWhere('s.dateLimiteInscription <= :fin');
            $queryBuilder->setParameter('fin', $sortiesData['fin']);
        }

        //filtre checkbox pour les sorties passées
        if ($sortiesData['passees'] == true) {
            $queryBuilder->andWhere('s.dateLimiteInscription < :aujourdhui');
            $queryBuilder->setParameter('aujourdhui', date("Y-m-d H:i:s"));
        }

        //filtre checkbox pour les sorties que j'organise
        if ($sortiesData['organisateur'] == true) {
            $queryBuilder->leftJoin('s.participant', 'participant');
            $queryBuilder->andWhere('participant.id = :moi');
            $queryBuilder->setParameter('moi', $security->getUser()->getId());
        }

        //filtre recherche par textfield
        if ($sortiesData['nom'] != null) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()->like('s.nom', ':nom')
            );
            $queryBuilder->setParameter('nom', '%' . $sortiesData['nom'] . '%');
        }

        if ($sortiesData['inscrit'] == true) {
            $queryBuilder->leftJoin('s.participants', 'participants');
            $queryBuilder->andWhere('participants.id = :moi');
            $queryBuilder->setParameter('moi', $security->getUser()->getId());
        }

        if ($sortiesData['noninscrit'] == true) {
            $user = $security->getUser()->getId();

            $queryBuilder2 = $this->createQueryBuilder('p');
            $queryBuilder2->select('p.participants.id');
            $queryBuilder2->from(Participant::class, 'pc');
            $queryBuilder2->where('pc.id != :user');
            $queryBuilder2->setParameter('user', $user);
            //dd($queryBuilder2->getDQL());

            $queryBuilder->andWhere($queryBuilder->expr()->notIn('s.id', $queryBuilder2->getDQL()));
            //dd($queryBuilder->getDQL());
        }


        $query = $queryBuilder->getQuery();
        //dd($query);
        $results = $query->getResult();
        return $results;
    }

    public function findByDate()
    {
        $queryBuilder = $this->createQueryBuilder('s');

        $queryBuilder->where('s.dateLimiteInscription > :dateLimite');
        $queryBuilder->setParameter('dateLimite', date('Y-m-d H:i:s', strtotime('-1 month')));

        $query = $queryBuilder->getQuery();
        //dd($query);
        $results = $query->getResult();
        //dd($results);
        return $results;
    }

    public function findSortieAdmin(string $query)
    {
        $qb = $this->createQueryBuilder('p');
        $qb
            ->where(
                $qb->expr()->andX(
                    $qb->expr()->orX(
                        $qb->expr()->like('p.nom', ':query'),

                    )
                )
            )
            ->setParameter('query', '%' . $query . '%');
        return $qb
            ->getQuery()
            ->getResult();
    }

}