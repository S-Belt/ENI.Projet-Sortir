<?php

namespace App\Controller;

use App\Repository\CampusRepository;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use App\Service\EtatService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Date;
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
     * @Route ("/sortie/annulerSortie/{id}", name="sortie_annulerSortie")
     */


    public function annulerSortie (int $id, EntityManagerInterface  $entityManager, SortieRepository $sortieRepository, EtatRepository $etatRepository){

//        trop d'erreur si on vérifie une seconde condition mais il faut quand même le faire

        $sortie= $sortieRepository->find($id);
        $etats= $etatRepository->findAll();

        if ($sortie->getEtat() !== $etats[3] ){

            $sortie->setEtat($etats[5]);
            $entityManager->persist($sortie);
            $entityManager->flush();
        } else {
            $this->addFlash('Erreur','Impossible d\'annuler cette sortie');
        }
        return $this->redirectToRoute('main_home');
    }


    /**
     * @Route("sortie/recherche", name="sortie_recherche")
     */
    public function recherche(SortieRepository $sortieRepository, Request $request, CampusRepository $campusRepository){
        $sortis = $sortieRepository->findAll();
        $campuss = $campusRepository->findAll();


        $campus = $_POST['selectCampus'];
        $nomContient = $_POST['contient'];
        $dateDebut = $_POST['entreA'];
        $dateFin = $_POST['entreB'];

        $organise = null;
        $inscrit = null;
        $nonInscrit = null;
        $passee = null;

        if(isset($_POST['organise'])){
            $organise = $this->getUser();
        }
        if(isset($_POST['inscrit'])){
            $inscrit = $this->getUser();
        }
        if(isset($_POST['nonInscrit'])){
            $nonInscrit = $this->getUser();
        }
        if(isset($_POST['passee'])){
            $demain = date_modify(new \DateTime(), '+1 day');
            $passee = $demain;
        }

        $resultats = $sortieRepository->recherche($campus, $nomContient, $dateDebut, $dateFin
                                                    ,$organise, $inscrit, $nonInscrit, $passee);


        return $this->render('main/home.html.twig', [
            'liste' => $resultats,
            'sortis' => $sortis,
            'campus' => $campuss
        ]);
    }



}
