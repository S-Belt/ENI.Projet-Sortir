<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Entity\Sortie;
use App\Form\ProfilType;
use App\Form\SortieFormType;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * @Route("/", name="main_")
 */
class MainController extends AbstractController
{
    /**
     * @Route("", name="home")
     */
    public function home(): Response
    {
        return $this->render('main/home.html.twig');
    }

    /**
     * @Route("profil/{id}", name="profil")
     */
    public function profil($id, ParticipantRepository $repository, Request $request){
        $participant = $repository->find($id);




        return $this->render("profil.html.twig", [
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
     $participant = new Participant();
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
                 throw $this->createNotFoundException('Ya un blem quequepart');
             }
             $participant->setPhotoFilename($newFilename);
         }

         $entityManager->persist($participant);
         $entityManager->flush();
         return $this->render("profil.html.twig", [
             'participant' => $participant
         ]);
     }
     return $this->render("monProfil.html.twig", ["form"=>$form->createView()]);

    }

    /**
     * @Route("creerSortie", name="creerSortie")
     */
    public function creerSortie(){
        $sortie = new Sortie();
        $sortieForm = $this->createForm(SortieFormType::class, $sortie);

        return $this->render('creerSortie.html.twig', [
            'sortieForm' => $sortieForm->createView()
        ]);
    }

}
