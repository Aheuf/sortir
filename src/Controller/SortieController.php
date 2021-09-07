<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\CreateSortieType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SortieController extends AbstractController
{
    /**
     * @Route("/sortie", name="sortie")
     */
    public function index(): Response
    {
        return $this->render('sortie/index.html.twig', [
        ]);
    }

    /**
     * @Route("/sortie/nouvelle_sortie", name="sortie_create")
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
}
