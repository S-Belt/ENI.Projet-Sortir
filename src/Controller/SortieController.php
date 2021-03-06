<?php

namespace App\Controller;

use App\Entity\Ville;
use App\Form\MotifAnnulationType;
use App\Form\VilleFormType;
use App\Repository\CampusRepository;
use App\Repository\EtatRepository;
use App\Repository\ParticipantRepository;
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
                $this->addFlash('success', 'Vous etes bien inscrit a cette sortie');

            } else {

                $this->addFlash('alert', 'Sorry, on peut pas s\'inscrire a cette sortie');

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

        $sortie= $sortieRepository->find($id);
        $etats= $etatRepository->findAll();


        if ($sortie->getEtat() !== $etats[3] ){

            $sortie->setEtat($etats[5]);
            $entityManager->persist($sortie);
            $entityManager->flush();
        } else {
            $this->addFlash('alert','Impossible d\'annuler cette sortie');
        }
        return $this->redirectToRoute('main_home');
    }


    /**
     * @Route("sortie/recherche", name="sortie_recherche")
     */
    public function recherche(SortieRepository $sortieRepository, Request $request, CampusRepository $campusRepository){
        $sortis = $sortieRepository->homePage();
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

    /**
     * @Route("sortie/motifAnnulation/{id}", name="sortie_motifAnnulation")
     */

    public function motifAnnulation(int $id,SortieRepository $sortieRepository,EntityManagerInterface $entityManager,Request $request){
       $sortie = $sortieRepository->find($id);
        $form = $this->createForm(MotifAnnulationType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $data = $form->getData();
            $motif = $data['motifAnnulation'];
            $sortie->setMotifAnnulation($motif);
            $entityManager->persist($sortie);
            $entityManager->flush();

            return $this->redirectToRoute('sortie_annulerSortie',['id'=>$id]);
        }


       return $this->render('main/motifAnnulation.html.twig',['form'=>$form->createView()]);
    }


    /**
     * @Route("sortie/ajouterVille", name="sortie_ajouterVille")
     */
    public function ajouterVille(EntityManagerInterface $entityManager, Request $request){
        $ville = new Ville();
        $villeForm = $this->createForm(VilleFormType::class, $ville);
        $villeForm->handleRequest($request);

        if($villeForm->isSubmitted() && $villeForm->isValid()){
            $entityManager->persist($ville);
            $entityManager->flush();

            return $this->redirectToRoute('main_creerSortie');
        }

        return $this->render("sortie/ajouterVille.html.twig", [
            "villeForme" => $villeForm->createView()
        ]);
    }

    /**
     * @Route("sortie/publier/{id}", name="sortie_publier")
     */
    public function publier($id, EtatService $service){
        $service->publier($id);

        return $this->redirectToRoute('main_afficher', ['id' => $id]);
    }

//    /**
//     * @Route("sortie/mesSortiesMobile/{id}", name="sortie_mesSortiesMobile")
//     */
//    public function mesSortiesMobile($id, SortieRepository $sortieRepository, ParticipantRepository $participantRepository, Request $request){
//        $sortie = $sortieRepository->find($id);
//        $participant = $participantRepository->find($id);
//        return $this->render("sortie/mesSortiesMobile.html.twig",[
//            'sortie' => $sortie, 'participant' => $participant
//        ]);
//
//    }

}
