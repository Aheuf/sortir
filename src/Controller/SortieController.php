<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Form\CreateSortieType;
use App\Repository\CampusRepository;
use App\Repository\SortieRepository;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\rechercheType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class SortieController extends AbstractController
{
    /**
     * @Route("/sortie", name="sortie")
     */
    public function index(Request $request, SortieRepository $sortieRepository, Security $security): Response
    {

        $sorties = $sortieRepository->findAll();
        $rechercheForm = $this->createForm(rechercheType::class);

        if($rechercheForm->handleRequest($request)->isSubmitted()) {
            $sortiesData = $rechercheForm->getData();
            //dd($sortiesData);
            $sorties = $sortieRepository->findByResearch($sortiesData, $security);
            //dd($sorties);
        }

        return $this->render('sortie/index.html.twig', [
            'rechercheForm' => $rechercheForm->createView(),
            'sorties' => $sorties,
        ]);
    }

    /**
     * @Route("/sortie/s_inscrire", name="sortie_sInscrire")
     */
    public function sInscrire(): Response
    {
        if ($this->getUser() != $user) {
            $this->addFlash('Warn', 'Vous ne pouvez pas inscrire un autre utilisateur !');
        } else {
            //Appel a la fonction sortie->addParticipant($participant)

            $this->addFlash('success', 'Votre inscription a bien été prise en compte !');
        }

        return $this->redirectToRoute('sortie');
    }

    /**
     * @Route("/sortie/se_desister", name="sortie_seDesister")
     */
    public function seDesister(): Response
    {
        if ($this->getUser() != $user) {
            $this->addFlash('Warn', 'Vous ne pouvez pas désinscrire un autre utilisateur !');
        } else {
            //Appel a la fonction sortie->removeParticipant($participant)

            $this->addFlash('success', 'Votre désinscription a bien été prise en compte !');
        }

        return $this->redirectToRoute('sortie');
    }

    /**
     * @Route("/sortie/creer_sortie/{id}", name="sortie_create")
     */
    public function create(int $id,
                           Request $request,
                           CampusRepository $campusRepository,
                           VilleRepository $villeRepository,
                           EntityManagerInterface $entityManager): Response
    {
        //notre entité vide
        $sortie = new Sortie();

        //recupere toutes les villes
        $villes = $villeRepository->findAll();

        //notre formulaire, associée à l'entité vide
        $sortieForm = $this->createForm(CreateSortieType::class, $sortie);

        //récupère les données du form et les injecte dans notre $sortie
        $sortieForm->handleRequest($request);

        //si le formulaire est soumis et valide...
        if ($sortieForm->isSubmitted() && $sortieForm->isValid()){

            //On récupère et stock l'info de l'id de l'organisateur
            $ligneOrganisateur = $this->getDoctrine()
                ->getRepository(Participant::class)
                ->find($id);
            $sortie->setParticipant($ligneOrganisateur);

            //récuperer et stocker dans la sortie le campus de l'organisateur
            $sortie->setCampus($ligneOrganisateur->getEstRattacheA());

            if ($sortieForm->getClickedButton() && 'save' === $sortieForm->getClickedButton()->getName()) {

                //On récupère l'info du clik pour passer l'état dans celui de la sortie créé
                $ligneEtat = $this->getDoctrine()
                    ->getRepository(Etat::class)
                    ->find(1);
                $sortie->setEtat($ligneEtat);

                //sauvegarde en bdd
                $entityManager->persist($sortie);
                $entityManager->flush();

                //affiche un message sur la prochaine page
                $this->addFlash('success', 'La sortie a été enregisté avec succès!');

            } else {
                //On récupère l'info du clik pour passer l'état dans celui de la sortie créé
                $ligneEtat = $this->getDoctrine()
                    ->getRepository(Etat::class)
                    ->find(2);
                $sortie->setEtat($ligneEtat);

                //sauvegarde en bdd
                $entityManager->persist($sortie);
                $entityManager->flush();

                //affiche un message sur la prochaine page
                $this->addFlash('success', 'La sortie a été publié avec succès!');
            }

            //redirige vers la page de détails de la sortie fraîchement créée
            return $this->redirectToRoute('sortie_detail', ['id' => $sortie->getId()]);
        }

        //affiche le formulaire
        return $this->render('sortie/create.html.twig', [
            'sortieForm' => $sortieForm->createView(),
            'villes' => $villes
        ]);
    }

    /**
     * @Route("/sortie/detail_sortie/{id}", name="sortie_detail")
     */
    public function detail(int $id,
                           CampusRepository $campusRepository,
                           SortieRepository $sortieRepository): Response
    {
        //récupère cette sortie en fonction de l'id présent dans l'URL
        $sortie = $sortieRepository->find($id);

        //s'il n'existe pas en bdd, on déclenche une erreur 404
        if (!$sortie){
            throw $this->createNotFoundException('Cette sortie n\'existe pas. Désolé!');
        }

        return $this->render('sortie/detail.html.twig', [
            "sortie" => $sortie // ->createView()
        ]);
    }

    /**
     * @Route("/sortie/modifier_sortie/{id}", name="sortie_modifier")
     */
    public function modify(int $id, SortieRepository $sortieRepository): Response
    {
        //todo

        return $this->render('sortie/modify.html.twig', [
            /////////"sortie" => $sortie
        ]);
    }

    /**
     * @Route("/sortie/annuler_sortie/{id}", name="sortie_annuler")
     */
    public function cancel(int $id, SortieRepository $sortieRepository): Response
    {
        //récupère cette sortie en fonction de l'id présent dans l'URL
        $sortie = $sortieRepository->find($id);

        //s'il n'existe pas en bdd, on déclenche une erreur 404
        if (!$sortie){
            throw $this->createNotFoundException('Cette sortie n\'existe pas. Désolé!');
        }

        return $this->render('sortie/cancel.html.twig', [
            "sortie" => $sortie // ->createView()
        ]);
    }

}