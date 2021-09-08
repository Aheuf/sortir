<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Sortie;
use App\Form\CreateSortieType;
use App\Repository\SortieRepository;
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
            $sorties = $sortieRepository->findByResearch($sortiesData, $security);
        }

        return $this->render('sortie/index.html.twig', [
            'rechercheForm' => $rechercheForm->createView(),
            'sorties' => $sorties,
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

        //récupère les données du form et les injecte dans notre $sortie
        $sortieForm->handleRequest($request);

        //si le formulaire est soumis et valide...
        if ($sortieForm->isSubmitted() && $sortieForm->isValid()){

            if ($sortieForm->getClickedButton() && 'save' === $sortieForm->getClickedButton()->getName()) {
                /*
                $sortie->setEtat($sortieForm->get('save')->isClicked()
                    ? 1
                    : 2
                );
                */

                //On récupère l'info de l'état pour le passer dans celui de la sortie créé
                $tableauEtat = $this->getDoctrine()
                    ->getRepository(Etat::class)
                    ->find(1);
                $sortie->setEtat($tableauEtat);
            }

            //sauvegarde en bdd
            $entityManager->persist($sortie);
            $entityManager->flush();

            //affiche un message sur la prochaine page
            $this->addFlash('success', 'La sortie a été ajoutée avec succès!');

            //redirige vers la page de détails de la sortie fraîchement créée
            return $this->redirectToRoute('sortie_detail', ['id' => $sortie->getId()]);
        }

        //affiche le formulaire
        return $this->render('sortie/create.html.twig', [
            "sortieForm" => $sortieForm->createView()
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
    public function delete(int $id, SortieRepository $sortieRepository): Response
    {
        //todo

        return $this->render('sortie/cancel.html.twig', [
            /////////////"sortie" => $sortie
        ]);
    }

}