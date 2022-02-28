<?php

namespace App\Service;

use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;

class EtatService
{
    private $entityManager;
    private $sortieRepository;
    private $etatRepository;


    public function __construct(EntityManagerInterface $entityManager, SortieRepository $sortieRepository, EtatRepository $etatRepository){
        $this->entityManager = $entityManager;
        $this->sortieRepository = $sortieRepository;
        $this->etatRepository = $etatRepository;
    }


    public function etat()
    {

        $sorties = $this->sortieRepository->findAll();
        $etats = $this->etatRepository->findAll();
        $dateActuelle = new \DateTime();
        $dateDemain = $dateActuelle->modify('+1 day');
        $aujourdhui = new \DateTime();



        foreach ($sorties as $sortie) {
            //Cloturée
            //Plus de places
            $dateFinDeSortie = new \DateTime;
            $dateFinDeSortie = $sortie->getDateHeureDebut();
            $interval = new \DateInterval('P1M');

           //$dateLimiteArchivage = $dateFinDeSortie->add($interval);



            if ($sortie->getParticipants()->count() === $sortie->getNbInscriptionMax()) {
                $sortie->setEtat($etats[2]);
                $this->entityManager->persist($sortie);
            }
            //Cloturée
            //Date inscription dépassée
            if ($sortie->getDateLimiteInscription() <= $dateActuelle) {
                $sortie->setEtat($etats[4]);
                $this->entityManager->persist($sortie);
            }
            //Activité en cours
            //Elle se deroule maintenant
            if ($sortie->getDateHeureDebut() >= $aujourdhui && $sortie->getDateHeureDebut() < $dateDemain) {
                $sortie->setEtat($etats[3]);
                $this->entityManager->persist($sortie);
            }

            if($aujourdhui > $dateLimiteArchivage){
                $sortie->setArchive(true);
                $this->entityManager->persist($sortie);

            }


        }
        $this->entityManager->flush();
        return $sorties;
    }

}