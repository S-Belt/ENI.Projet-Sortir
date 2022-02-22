<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\ProfilType;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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

         $entityManager->persist($participant);
         $entityManager->flush();
         return $this->render("profil.html.twig", [
             'participant' => $participant
         ]);
     }
     return $this->render("monProfil.html.twig", ["form"=>$form->createView()]);

    }
}
