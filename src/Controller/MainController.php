<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Form\LieuFormType;
use App\Form\ProfilType;
use App\Form\SortieFormType;
use App\Repository\CampusRepository;
use App\Repository\EtatRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use App\Service\EtatService;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * @IsGranted("ROLE_USER")
 * @Route("/", name="main_")
 */
class MainController extends AbstractController
{
    /**
     * @Route("", name="home")
     */
    public function home(CampusRepository $repository
                            , EtatService $service): Response
    {

        $liste = $service->etat();

        $campus = $repository->findAll();

        //test. Le service recupere deja la liste.
        //$liste = $sortieRepository->findAll();

        return $this->render('main/home.html.twig', [
            'campus' => $campus,
            'liste' => $liste,

        ]);


    }





    /**
     * @Route("profil/{id}", name="profil")
     */
    public function profil($id, ParticipantRepository $repository, Request $request){
        $participant = $repository->find($id);




        return $this->render("main/profil.html.twig", [
            'participant' => $participant
        ]);
    }

    /**
     * @Route("monProfil/{id}", name="monProfil")
     */
    public function monProfil ($id, ParticipantRepository $repository,
                               Request $request, EntityManagerInterface $entityManager,
                               UserPasswordHasherInterface $userPasswordHasher,
                               SluggerInterface $slugger) {

     $participant = $repository->find($id);
     

     $form = $this->createForm(ProfilType::class, $participant);
     $form->handleRequest($request);

     if ( $form->isSubmitted() && $form-> isValid()) {

         $participant->setPassword(
             $userPasswordHasher->hashPassword(
                 $participant,
                 $form->get('password')->getData()
             )
         );

         //photo de profil
         $photoFile = $form->get('photo')->getData();

         if($photoFile){
             $originalFilename = pathinfo($photoFile->getClientOriginalName(), PATHINFO_FILENAME);
             $safeFilename = $slugger->slug($originalFilename);
             $newFilename = $safeFilename.'-'.uniqid().'.'.$photoFile->guessExtension();

             try{
                 $photoFile->move(
                     $this->getParameter('photo_directory'),
                     $newFilename
                 );
             }catch (FileException $exception){
                 throw $this->createNotFoundException('Il y a eu un probleme quelque part...');
             }
             $participant->setPhotoFilename($newFilename);
         }

         $entityManager->persist($participant);
         $entityManager->flush();
         return $this->render("main/profil.html.twig", [
             'participant' => $participant
         ]);
     }
     return $this->render("main/monProfil.html.twig", ["form"=>$form->createView()]);

    }

    /**
     * @Route("creerSortie", name="creerSortie")
     */
    public function creerSortie(Request $request, EntityManagerInterface $entityManager, EtatRepository $repository){
        $organisateur = $this->getUser();
        $sortie = new Sortie();
        $sortie ->setArchive(false);

        $etatCree = $repository->find(1);
        $etatOuverte = $repository->find(2);


        $sortieForm = $this->createForm(SortieFormType::class, $sortie);


        $sortieForm->handleRequest($request);

        $sortie->setOrganisateur($organisateur);

        if($sortieForm->isSubmitted() && $sortieForm->isValid()){
            $bouton = $request->request->get('bouton');

            if($bouton === 'Enregistrer'){
                $sortie->setEtat($etatCree);
            }else{
                $sortie->setEtat($etatOuverte);
            }

            $entityManager->persist($sortie);
            $entityManager->flush();
            return $this->redirectToRoute('main_home');
        }

        return $this->render('main/creerSortie.html.twig', [
            'sortieForm' => $sortieForm->createView()
        ]);
    }

    /**
     * @Route("creerLieu", name="creerLieu")
     */
    public function creerLieu(Request $request, EntityManagerInterface $entityManager){
        $lieu = new Lieu();
        $sortie = new Sortie();
        $lieuForm = $this->createForm(LieuFormType::class, $lieu);
        $sortieForm = $this->createForm(SortieFormType::class, $sortie);
        $lieuForm->handleRequest($request);

        if($lieuForm->isSubmitted() && $lieuForm->isValid()){
            $entityManager->persist($lieu);
            $entityManager->flush();
            return $this->render("main/creerSortie.html.twig", [ 'sortieForm' => $sortieForm->createView()]);
        }


        return $this->render("main/creerLieu.html.twig", [ 'lieuForm' => $lieuForm->createView()]);
    }

    /**
     * @Route("afficher/{id}", name="afficher")
     */
    public function afficher($id, SortieRepository $sortieRepository){
        $sortie = $sortieRepository->find($id);
        return $this->render("main/afficher.html.twig",[
            'sortie' => $sortie
        ]);

    }



}
