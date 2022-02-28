<?php

namespace App\Controller;

use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
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


    
    
}
