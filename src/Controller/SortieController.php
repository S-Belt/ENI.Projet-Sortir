<?php

namespace App\Controller;

use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SortieController extends AbstractController
{
    /**
     * @Route("/sortie/sinscrire/{id}", name="sortie_sinscrire")
     */
    public function sinscrire(int $id, SortieRepository $sortieRepository, EntityManagerInterface $entityManager): Response
    {
        $sortie= $sortieRepository->find($id);
        $participant= $this->getUser();


        if ($sortie->getParticipants()->count()<$sortie->getNbInscriptionMax()) {

            if ($sortie->getEtat() === 'Ouverte') {

                $sortie->addParticipant($participant);
                $entityManager->persist($sortie);
                $entityManager->flush();
                $this->addFlash('alert', 'Vous etes bien inscrit a cette sortie');

            } else {

                $this->addFlash('alert', 'Sorry, on peut plus s\'inscrire a cette sortie');

            }
        }else {
                $this->addFlash('alert', 'Sorry, il n\'y a plus de place disponible pour cette sortie');
            }

        return $this->redirectToRoute('main_afficher',['id'=>$sortie->getId()]);
    }

    /**
     * @Route ("/sortie/etat", name="sortie_etat")
     */

    public function etat(SortieRepository $sortieRepository, EtatRepository $etatRepository){

        $sorties= $sortieRepository->findAll();
        $etats= $etatRepository->findAll();
        $dateActuelle= new \DateTime();


        foreach ($sorties as $sortie){

            $dateDeFin=$sortie->(getDateHeureDebut() + );

            if ($sortie->getParticipants()->count()===$sortie->getNbInscriptionMax()){
                $sortie->setEtat($etats[2]);
            }
            if($sortie->getDateHeureDebut()>=$dateActuelle){

                $sortie->setEtat($etats[3]);
            }
            if($sortie->getDateLimiteInscription()>=$dateActuelle){

                $sortie->setEtat($etats[2]);
            }
            if(){

            }

                
            
            
            
        }




    }


}
