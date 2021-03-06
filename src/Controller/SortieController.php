<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Form\CreateSortieType;
use App\Form\LieuType;
use App\Form\UpdateSortieType;
use App\Repository\CampusRepository;
use App\Repository\EtatRepository;
use App\Repository\LieuRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManager;
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

        $sorties = $sortieRepository->findByDate();
        $rechercheForm = $this->createForm(rechercheType::class);

        if ($rechercheForm->handleRequest($request)->isSubmitted()) {
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
     * @Route("/sortie/s_inscrire/{sortieId}/{userId}", name="sortie_sInscrire")
     */
    public function sInscrire($sortieId, $userId, SortieRepository $sortieRepository, ParticipantRepository $participantRepository, EntityManagerInterface $entityManager): Response
    {
        $sortie = $sortieRepository->find($sortieId);
        $user = $participantRepository->find($userId);
        if ($this->getUser() != $user) {
            $this->addFlash('Warn', 'Vous ne pouvez pas inscrire un autre utilisateur !');
        } else {
            $sortie->addParticipant($user);
            $user->addEstInscrit($sortie);
            //dd($sortie->getParticipants()->toArray());
            $entityManager->flush();

            $this->addFlash('success', 'Votre inscription a bien ??t?? prise en compte !');
        }

        return $this->redirectToRoute('sortie');
    }

    /**
     * @Route("/sortie/se_desister/{sortieId}/{userId}", name="sortie_seDesister")
     */
    public function seDesister($sortieId, $userId, SortieRepository $sortieRepository, ParticipantRepository $participantRepository, EntityManagerInterface $entityManager): Response
    {
        $sortie = $sortieRepository->find($sortieId);
        $user = $participantRepository->find($userId);
        if ($this->getUser() != $user) {
            $this->addFlash('Warn', 'Vous ne pouvez pas d??sinscrire un autre utilisateur !');
        } else {
            $sortie->removeParticipant($user);
            $user->removeEstInscrit($sortie);

            $entityManager->flush();

            $this->addFlash('success', 'Votre d??sinscription a bien ??t?? prise en compte !');
        }

        return $this->redirectToRoute('sortie');
    }

    /**
     * @Route("/sortie/publier/{sortieId}/{userId}", name="sortie_publier")
     */
    public function publier($sortieId, $userId, SortieRepository $sortieRepository, EtatRepository $etatRepository, ParticipantRepository $participantRepository, EntityManagerInterface $entityManager): Response
    {
        $sortie = $sortieRepository->find($sortieId);
        $user = $participantRepository->find($userId);
        if ($this->getUser() != $user) {
            $this->addFlash('Warn', 'Vous ne pouvez pas publier la sortie d\'un autre utilisateur !');
        } else {

            $etat = $etatRepository->findOneBy(['libelle' => 'Ouverte']);

            $sortie->setEtat($etat);

            $entityManager->flush();

            $this->addFlash('success', 'Votre sortie a bien ??t?? publi??e !');
        }

        return $this->redirectToRoute('sortie');
    }

    /**
     * @Route("/sortie/creer_sortie/{idOrganisateur}", name="sortie_create")
     */
    public function create(int                    $idOrganisateur,
                           Request                $request,
                           CampusRepository       $campusRepository,
                           VilleRepository        $villeRepository,
                           LieuRepository         $lieuRepository,
                           EntityManagerInterface $entityManager): Response
    {
        //notre entit?? vide
        $sortie = new Sortie();
        $lieu = new Lieu();

        //recupere toutes les villes
        $villes = $villeRepository->findAll();

        //notre formulaire, associ??e ?? l'entit?? vide
        $sortieForm = $this->createForm(CreateSortieType::class, $sortie);
        $lieuForm = $this->createForm(LieuType::class, $lieu);

        //r??cup??re les donn??es du form et les injecte dans notre $sortie
        $sortieForm->handleRequest($request);
        $lieuForm->handleRequest($request);

        //si le formulaire de lieu est soumis et valide...
        if ($request->getMethod() == "POST") {
            if ($request->request->get("submitLieuAction") == "Enregistrement") {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($lieu);
                $entityManager->flush();

                $this->addFlash('success', 'Le lieu ?? ??t?? cr????');
                return $this->redirect($_SERVER['HTTP_REFERER']);
            }
        }

        //si le formulaire est soumis et valide...
        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {

            //On r??cup??re et stock l'info de l'id de l'organisateur
            $ligneOrganisateur = $this->getDoctrine()
                ->getRepository(Participant::class)
                ->find($idOrganisateur);
            $sortie->setParticipant($ligneOrganisateur);

            //r??cuperer et stocker dans la sortie le campus de l'organisateur
            $sortie->setCampus($ligneOrganisateur->getEstRattacheA());

            if ($sortieForm->getClickedButton() && 'save' === $sortieForm->getClickedButton()->getName()) {

                //On r??cup??re l'info du clik pour passer l'??tat dans celui de la sortie cr????
                $ligneEtat = $this->getDoctrine()
                    ->getRepository(Etat::class)
                    ->findOneBy(['libelle' => 'Cr????e']);
                $sortie->setEtat($ligneEtat);

                //sauvegarde en bdd
                $entityManager->persist($sortie);
                $entityManager->flush();

                //affiche un message sur la prochaine page
                $this->addFlash('success', 'La sortie a ??t?? enregist?? avec succ??s!');

            } else {
                //On r??cup??re l'info du clik pour passer l'??tat dans celui de la sortie cr????
                $ligneEtat = $this->getDoctrine()
                    ->getRepository(Etat::class)
                    ->findOneBy(['libelle' => 'Ouverte']);
                $sortie->setEtat($ligneEtat);

                //sauvegarde en bdd
                $entityManager->persist($sortie);
                $entityManager->flush();

                //affiche un message sur la prochaine page
                $this->addFlash('success', 'La sortie a ??t?? publi?? avec succ??s!');
            }

            //redirige vers la page de d??tails de la sortie fra??chement cr????e
            return $this->redirectToRoute('sortie_detail', ['id' => $sortie->getId()]);
        }

        //affiche le formulaire
        return $this->render('sortie/create.html.twig', [
            'sortieForm' => $sortieForm->createView(),
            'villes' => $villes,
            'lieuForm' => $lieuForm->createView()
        ]);
    }

    /**
     * @Route("/sortie/detail_sortie/{id}", name="sortie_detail")
     */
    public function detail(int              $id,
                           //ParticipantRepository $participantRepository,
                           SortieRepository $sortieRepository): Response
    {
        //r??cup??re cette sortie en fonction de l'id pr??sent dans l'URL
        $sortie = $sortieRepository->find($id);

        //recupere toutes les particpants de cette sortie
        //$particpants = $participantRepository->findAll();

        //s'il n'existe pas en bdd, on d??clenche une erreur 404
        if (!$sortie) {
            throw $this->createNotFoundException('Cette sortie n\'existe pas. D??sol??!');
        }

        return $this->render('sortie/detail.html.twig', [
            "sortie" => $sortie,
            //"participants" => $particpants
        ]);
    }

    /**
     * @Route("/sortie/modifier_sortie/{id}", name="sortie_modifier")
     */
    public function modify(int $id, Request $request, SortieRepository $sortieRepository, EtatRepository $repository, EntityManagerInterface $entityManager): Response
    {

        $sortie = $sortieRepository->find($id);
        $form = $this->createForm(UpdateSortieType::class, $sortie);

        $form->handleRequest($request);
        if (!$form->isSubmitted()) {
            $form['dateHeureDebut']->setData($sortie->getDateHeureDebut());
            $form['dateLimiteInscription']->setData($sortie->getDateLimiteInscription());
        }

        if ($form->isSubmitted() && $form->isValid()) {

            if ($form->getClickedButton() && 'save' === $form->getClickedButton()->getName()) {
                $sortie->setEtat($repository->findOneBy(['libelle' => 'Cr????e']));
            } else {
                $sortie->setEtat($repository->findOneBy(['libelle' => 'Ouverte']));
            }
            $entityManager->flush();
            return $this->redirectToRoute('sortie_detail', ['id' => $sortie->getId()]);
        }

        return $this->render('sortie/modify.html.twig', ["sortie" => $sortie, 'UpdateSortieForm' => $form->createView()]);
    }

    /**
     * @Route("/sortie/annuler_sortie/{id}", name="sortie_annuler")
     */
    public function cancel(int $id, SortieRepository $sortieRepository, EtatRepository $repository, EntityManagerInterface $entityManager): Response
    {
        //r??cup??re cette sortie en fonction de l'id pr??sent dans l'URL
        $sortie = $sortieRepository->find($id);
        $etat = $repository->findOneBy(['libelle' => 'Annul??e']);
        $sortie->setEtat($etat);
        $entityManager->flush();

        //s'il n'existe pas en bdd, on d??clenche une erreur 404
        if (!$sortie) {
            throw $this->createNotFoundException('Cette sortie n\'existe pas. D??sol??!');
        }
        $this->addFlash('danger', 'la sortie ?? ??t?? supprim??e');
        return $this->redirectToRoute('sortie');
    }

}