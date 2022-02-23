<?php

namespace App\Controller;

use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\DateTime;

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

            if ($sortie->getEtat()->getLibelle() === 'Ouverte') {

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
     * @Route ("/sortie/seDesister/{id}", name="sortie_seDesister")
     */

    public function seDesister(int $id, SortieRepository $sortieRepository, EntityManagerInterface $entityManager) {

        $sortie= $sortieRepository->find($id);
        $participant= $this->getUser();

        $sortie->removeParticipant($participant);
        $entityManager->persist($sortie);
        $entityManager->flush();

        return $this->redirectToRoute('main_afficher',['id'=>$sortie->getId()]);

    }


    /**
     * @Route ("/sortie/etat", name="sortie_etat")
     */

    public function etat(SortieRepository $sortieRepository, EtatRepository $etatRepository,EntityManagerInterface $entityManager){

        $sorties= $sortieRepository->findAll();
        $etats= $etatRepository->findAll();
        $dateActuelle= new \DateTime();


        foreach ($sorties as $sortie){

//            $dateDeFin=$sortie->getDateHeureDebut() + );
//            $duree = $sortie ->getDuree();
//            $debut = $sortie ->getDateHeureDebut();
//            date('Y-m-d H:i',strtotime($debut,$duree));
//            $interval = new \DateInterval('PT4H');
//            Datetime::add($debut);



            if ($sortie->getParticipants()->count()===$sortie->getNbInscriptionMax()){
                $sortie->setEtat($etats[2]);
                $entityManager->persist($sortie);
            }
            if($sortie->getDateHeureDebut()>=$dateActuelle){

                $sortie->setEtat($etats[3]);
                $entityManager->persist($sortie);
            }
            if($sortie->getDateLimiteInscription()>=$dateActuelle){

                $sortie->setEtat($etats[2]);
                $entityManager->persist($sortie);
            }

        }
        $entityManager->flush();
        return $this->redirectToRoute('main_home');
    }

    /**
     * @Route("sortie/recherche", name="sortie_recherche")
     */
    public function recherche(SortieRepository $sortieRepository, Request $request){
        $campus = $sortieRepository->findAll();


        $campus = $_POST['selectCampus'];
        $nomContient = $_POST['contient'];
        $dateDebut = $_POST['entreA'];
        $dateFin = $_POST['entreB'];

        $organise = null;
        $inscrit = null;
        $nonInscrit = null;
        $passee = null;

        if(isset($_POST['organise'])){
            $organise = true;
        }
        //doit etre le pseudo du connecté
        if(isset($_POST['inscrit'])){
            $inscrit = true;
        }
        if(isset($_POST['nonInscrit'])){
            $nonInscrit = true;
        }
        if(isset($_POST['passee'])){
            $passee = true;
        }

        $resultats = $sortieRepository->recherche($campus, $nomContient, $dateDebut, $dateFin
                                                    ,$organise, $inscrit, $nonInscrit, $passee);

        return $this->render('main/home.html.twig', [
            'liste' => $resultats,
            'campus' => $campus
        ]);
    }

}
