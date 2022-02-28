<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\AjoutType;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;


/**
 *@IsGranted("ROLE_ADMIN")
 * @Route("/admin", name="admin_")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */

    public function home(EntityManagerInterface $entityManager, ParticipantRepository $participantRepository): Response
    {
        $tabParticipants=$participantRepository->findAll();



        return $this->render('admin/home.html.twig', [
            'tabParticipants' => $tabParticipants
        ]);
    }

    /**
     * @Route ("/supprimer/{id}", name="supprimer")
     */
    
    public function supprimerParticipant(int $id, EntityManagerInterface $entityManager, ParticipantRepository $participantRepository):Response
    {
        $participantAEffacer=$participantRepository->find($id);
        $pseudo=$participantAEffacer->getPseudo();
        $entityManager->remove($participantAEffacer);
        $entityManager->flush();
        $this->addFlash('alert', $pseudo.' à bien été supprimé');

    return $this->redirectToRoute('admin_home');
    }

    /**
     * @Route ("/actif/{id}", name="actif")
     */

    public function actifParticipant(int $id, EntityManagerInterface $entityManager, ParticipantRepository $participantRepository):Response
    {
        $participant=$participantRepository->find($id);
        $actif=$participant->getActif();
        if($actif)
        {
           $participant->setActif(false);
        }
        else{
            $participant->setActif(true);
        }

        $entityManager->persist($participant);
        $entityManager->flush();
        $this->addFlash('success', $participant->getPseudo().' à bien été modifié');

    return $this->redirectToRoute('admin_home');
    }

    /**
     * @Route("/ajouter", name="ajouter")
     */

    public function ajouterParticipant(EntityManagerInterface $entityManager, Request $request, UserPasswordHasherInterface $userPasswordHasher)
    {
        $participant = new Participant();
        $form= $this->createForm(AjoutType::class, $participant);
        $participant->setAdministrateur(false);
        $participant->setPhotoFilename('defautProfil.png');
        $participant->setRoles(['ROLE_USER']);
        $form->handleRequest($request);


        if($form->isSubmitted() && $form->isSubmitted()){

            $participant->setPassword(
                $userPasswordHasher->hashPassword(
                    $participant,
                    $form->get('password')->getData()
                ));

            $entityManager->persist($participant);
            $entityManager->flush();
            $this->addFlash('success', 'L\'utilisateur à bien été crée');
            return $this->redirectToRoute('admin_home');
        }
        return $this->render('admin/ajouter.html.twig', ['form' => $form->createView()]);



    }


    
    
}
