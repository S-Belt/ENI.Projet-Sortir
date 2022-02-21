<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\ProfilType;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
    public function monProfil ($id, ParticipantRepository $repository, Request $request, EntityManagerInterface $entityManager) {
     $participant = new Participant();
     $form = $this->createForm(ProfilType::class, $participant);
     $form->handleRequest($request);

     if ($form-> isValid() && $form->isSubmitted()) {
         $entityManager->persist($participant);
         $entityManager->flush();
         return $this->render("profil.html.twig");
     }

    }
}
