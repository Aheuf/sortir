<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\CreateSortieType;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\rechercheType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SortieController extends AbstractController
{
    /**
     * @Route("/sortie", name="sortie")
     */
    public function index(Request $request): Response
    {
        $rechercheForm = $this->createForm(rechercheType::class);

        $rechercheForm->handleRequest($request);


        return $this->render('sortie/index.html.twig', [
            'rechercheForm' => $rechercheForm->createView()
        ]);
    }

    /**
     * @Route("/sortie/creer_sortie", name="sortie_create")
     */
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        //notre entité vide
        $sortie = new Sortie();

        //notre formulaire, associée à l'entité vide
        $sortieForm = $this->createForm(CreateSortieType::class, $sortie);

        //récupère les données du form et les injecte dans notre $wish
        $sortieForm->handleRequest($request);

        //si le formulaire est soumis et valide...
        if ($sortieForm->isSubmitted() && $sortieForm->isValid()){
            //hydrate les propriétés absentes du formulaires
            $sortie->setNom(true);
            $sortie->setDateHeureDebut(new \DateTime());
            $sortie->setDateLimiteInscription(new \DateTime());
            $sortie->setNbInscriptionMax(true);
            $sortie->setDuree(true);
            $sortie->setInfoSortie(true);
            //d'autre à rajouter???

            //sauvegarde en bdd
            $entityManager->persist($sortie);
            $entityManager->flush();

            //affiche un message sur la prochaine page
            $this->addFlash('success', 'La sortie a été ajouté avec succès!');

            //redirige vers la page de détails de l'idée fraîchement créée
            return $this->redirectToRoute('detail_sortie', ['id' => $sortie->getId()]);
        }

        //affiche le formulaire
        return $this->render('sortie/create.html.twig', [
            "sortieForm" => $sortieForm->createView()
        ]);
    }

    /**
     * @Route("/sortie/liste_sortie", name="sortie_list")
     */
    public function list(SortieRepository $sortieRepository): Response
    {
        //récupère les sorties publiés, de la plus récente à la plus ancienne
        //on appelle une méthode personnalisée ici pour éviter d'avoir trop de requêtes.
        //Voir le SortieRepository.php
        /////////$sorties = $sortieRepository->findPublishedWishesWithCategories();

        return $this->render('sortie/list.html.twig', [
            //les passe à Twig
            //////////////"sorties" => $sorties
        ]);
    }

    /**
     * @Route("/sortie/detail_sortie/{id}", name="sortie_detail")
     */
    public function detail(int $id, SortieRepository $sortieRepository): Response
    {
        //récupère cete sortie en fonction de l'id présent dans l'URL
        $sortie = $sortieRepository->find($id);

        //s'il n'existe pas en bdd, on déclenche une erreur 404
        if (!$sortie){
            throw $this->createNotFoundException('Cette sortie n\'existe pas. Désolé!');
        }

        return $this->render('sortie/detail.html.twig', [
            "sortie" => $sortie
        ]);
    }

    /**
     * @Route("/sortie/annuler_sortie/{id}", name="sortie_annuler")
     */
    public function delete(int $id, SortieRepository $sortieRepository): Response
    {
        //todo

        return $this->render('sortie/delete.html.twig', [
            /////////////"sortie" => $sortie
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
}