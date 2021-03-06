<?php

namespace App\Repository;

use App\Entity\Participant;
use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

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
    public function liste($value)
    {
        $queryBuilder = $this->createQueryBuilder('qb');
        $queryBuilder
            ->addSelect()
            ->from('App:Sortie', 's')
            ->leftJoin('s.participants', 'u')
            ->where('s.id = :id')
            ->setParameter('id', $value);



        $query = $queryBuilder->getQuery();
        return $query->getResult();
    }


    //methode pour moins de requetes ( marche pas encore tres bien)
    public function accueil(Participant $participant){
        $queryBuilder = $this->createQueryBuilder('q')
            ->select('s', 'p')
            ->from('App:Sortie', 's')
            ->leftJoin('s.organisateur', 'p')
            ->where('p = :participant')
            ->setParameter(':participant', $participant);

        return $queryBuilder->getQuery()->getResult();

    }



    public function recherche($campus = null, $contient = null, $dateDebut = null, $dateFin = null,
                                $organise = null, $inscrit = null, $nonInscrit = null,
                                    $passee = null){
        $queryBuilder = $this->createQueryBuilder('s');
        $queryBuilder->andWhere('s.campus = :campus')
                    ->setParameter(':campus', $campus);
        if($contient){
            $queryBuilder->andWhere('s.nom LIKE :contient')
                ->setParameter(':contient', '%'.$contient.'%');
        }
        if($dateDebut){
            $queryBuilder->andWhere('s.dateHeureDebut >= :dateDebut')
                            ->setParameter(':dateDebut', $dateDebut);
        }
        if($dateFin){
            $queryBuilder->andWhere('s.dateHeureDebut <= :dateFin')
                ->setParameter(':dateFin', $dateFin);
        }
        if($organise){
            $queryBuilder->andWhere('s.organisateur = :organise')
                            ->setParameter(':organise', $organise);
        }
        if($inscrit){
            $queryBuilder->andWhere(':inscrit MEMBER OF s.participants')
                            ->setParameter(':inscrit', $inscrit);
        }
        if($nonInscrit){
            $queryBuilder->andWhere(':nonInscrit NOT MEMBER OF s.participants')
                            ->setParameter(':nonInscrit', $nonInscrit);
        }
        if($passee){
            $queryBuilder->andWhere('s.dateHeureDebut < :passee')
                            ->setParameter(':passee', $passee);
        }

        $query = $queryBuilder->getQuery();
        return $query->getResult();
    }

    public function homePage()
    {
        $queryBuilder = $this->createQueryBuilder('s');

        $queryBuilder
    ->select('s', 'p')
            //->addSelect('s.nom', 's.dateHeureDebut', 's.dateLimiteInscription','org.pseudo','p.pseudo')
            ->leftJoin('s.organisateur', 'org')
            //->addSelect('org.pseudo') //pour qu'il fasse qu'une seule requete
            ->leftJoin('s.etat', 'etat')
            //->addSelect('etat.libelle')
            ->leftJoin('s.participants', 'p');
            //->addSelect('p.pseudo');

        $query = $queryBuilder->getQuery();

        $results = $query->getResult();
        return $results;

    }
}
