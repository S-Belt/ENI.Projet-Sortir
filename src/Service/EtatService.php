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

            $dateFinDeSortie = new \DateTime;
            $dateFinDeSortie = $sortie->getDateHeureDebut();
            $interval = new \DateInterval('P1M');

            //$dateLimiteArchivage = $dateFinDeSortie->add($interval);

           $debutSortie = $sortie->getDateHeureDebut()->format("d-m-Y");
           $dateAujourdhui = new \DateTime();
           $dateAujourdhui->format("d-m-Y");










            //Cloturée
            //Plus de places
            if ($sortie->getParticipants()->count() === $sortie->getNbInscriptionMax()) {
                $sortie->setEtat($etats[2]);
                $this->entityManager->persist($sortie);
            }
            //Activité en cours
            //Elle se deroule maintenant
            /*elseif ($sortie->getDateHeureDebut() >= $aujourdhui && $sortie->getDateHeureDebut() < $dateDemain) {
                $sortie->setEtat($etats[3]);
                $this->entityManager->persist($sortie);
            }*/
            //Activité en cours
            //Elle se deroule maintenant
            elseif ($debutSortie == $dateAujourdhui->format("d-m-Y") ){
                $sortie->setEtat($etats[3]);
                $this->entityManager->persist($sortie);
            }
            //Passée
            //Date inscription dépassée
            elseif ($sortie->getDateLimiteInscription() <= $dateActuelle) {
                $sortie->setEtat($etats[4]);
                $this->entityManager->persist($sortie);
            }
            //Créee
            //Si elle est à l'état Créee,
            //elle le reste
            elseif($sortie->getEtat() === $etats[0]){
                $sortie->setEtat($etats[0]);
                $this->entityManager->persist($sortie);
            }
            //Ouverte
            //Si elle n'est rentrée dans aucun traitement
            //c'est qu'elle est ouverte
            else{
                $sortie->setEtat($etats[1]);
                $this->entityManager->persist($sortie);
            }


            //Archivée
            //Si la sortie a eu lieu il a plus d'un mois
            $sortieArchive = date('Y-m-d', strtotime("$debutSortie + 1 month"));
            if($sortieArchive < $dateAujourdhui->format('Y-m-d')){
                $sortie->setArchive(true);
                $this->entityManager->persist($sortie);
            }



        }
        $this->entityManager->flush();
        return $sorties;
    }

}