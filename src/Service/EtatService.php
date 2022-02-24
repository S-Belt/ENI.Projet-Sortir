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


    public function etat(){

        $sorties= $this->sortieRepository->findAll();
        $etats= $this->etatRepository->findAll();
        $dateActuelle= new \DateTime();
        $dateDemain = $dateActuelle->modify('+1 day');



        foreach ($sorties as $sortie){
            //Cloturée
            //Plus de places
            if ($sortie->getParticipants()->count()===$sortie->getNbInscriptionMax()){
                $sortie->setEtat($etats[2]);
                $this->entityManager->persist($sortie);
            }
            //Cloturée
            //Date inscription dépassée
            if($sortie->getDateLimiteInscription()>=$dateActuelle){
                $sortie->setEtat($etats[2]);
                $this->entityManager->persist($sortie);
            }
            //Activité en cours
            //Elle se deroule maintenant
            if($sortie->getDateHeureDebut()>=$dateActuelle && $sortie->getDateHeureDebut()< $dateDemain){
                $sortie->setEtat($etats[3]);
                $this->entityManager->persist($sortie);
            }


        }
        $this->entityManager->flush();
        return $sorties;
    }


}