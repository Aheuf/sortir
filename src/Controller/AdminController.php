<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Entity\Ville;
use App\Form\CampusType;
use App\Form\VilleType;
use App\Repository\CampusRepository;
use App\Repository\VilleRepository;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin", name="admin_")
 */
class AdminController extends AbstractController
{

    /**
     * @Route("/villes", name="villes")
     */
    public function villes(VilleRepository $villeRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createFormBuilder()
            ->add('query', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Entrez un mot-clé'
                ]
            ])
            ->add('recherche', SubmitType::class, [
                'attr' => [

                    'class' => 'btn btn-outline-secondary']
            ])
            ->getForm();


        $createVille = new Ville();
        $VilleForm = $this->createForm(VilleType::class, $createVille);
        $VilleForm->handleRequest($request);

        if (isset($request->request->get('form')['query'])) {
            $query = $request->request->get('form')['query'];
            $ville = $villeRepository->findVillesByName($query);

        } else {
            $ville = $villeRepository->findBy([], ['nom' => 'DESC']);

        }


        if ($VilleForm->isSubmitted() && $VilleForm->isValid()) {
            $entityManager->persist($createVille);
            $entityManager->flush();

            return $this->redirect($_SERVER['HTTP_REFERER']);
        }

        return $this->render('admin/villes.html.twig', [
            'villes' => $ville, 'VilleForm' => $VilleForm->createView(), 'form' => $form->createView()
        ]);

    }

    /**
     * @Route("/update_villes/{id}", name="update_ville")
     */
    public function UpdateVille(Request $request, int $id): Response
    {
        $em = $this->getDoctrine()->getManager();
        $ville = $em->getRepository('App:Ville')->find($id);
        $valeurVille = "ville" . $id;
        $valeurCp = "cp" . $id;
        $changementNomVille = $_POST[($valeurVille)];
        $changementCpVille = $_POST[($valeurCp)];

        $ville->setNom($changementNomVille);
        $ville->setCodePostal($changementCpVille);
        $em->flush();
        return $this->redirect($_SERVER['HTTP_REFERER']);
    }

    /**
     * @Route("/delete_villes/{id}", name="delete_ville")
     */
    public function DeleteVille(int $id): Response
    {
        $em = $this->getDoctrine()->getManager();
        $ville = $em->getRepository('App:Ville')->find($id);
        $em->remove($ville);
        $em->flush();

        return $this->redirect($_SERVER['HTTP_REFERER']);
    }


    /**
     * @Route("/campus", name="campus")
     */
    public function campus(CampusRepository $campusRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createFormBuilder()
            ->add('query', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Entrez un mot-clé'
                ]
            ])
            ->add('recherche', SubmitType::class, [
                'attr' => [

                    'class' => 'btn btn-outline-secondary']
            ])
            ->getForm();

        $createCampus = new Campus();
        $CampusForm = $this->createForm(CampusType::class, $createCampus);
        $CampusForm->handleRequest($request);

        if (isset($request->request->get('form')['query'])) {
            $query = $request->request->get('form')['query'];
            $campus = $campusRepository->findCampusByName($query);

        } else {
            $campus = $campusRepository->findBy([], ['nom' => 'DESC']);

        }

        if ($CampusForm->isSubmitted() && $CampusForm->isValid()) {
            $entityManager->persist($createCampus);
            $entityManager->flush();

            return $this->redirect($_SERVER['HTTP_REFERER']);
        }

        return $this->render('admin/campus.html.twig', [
            'campus' => $campus, 'CampusForm' => $CampusForm->createView(), 'formCampus' => $form->createView()
        ]);

    }


    /**
     * @Route("/update_campus/{id}", name="update_campus")
     */
    public function UpdateCampus(Request $request, int $id): Response
    {
        $em = $this->getDoctrine()->getManager();
        $ville = $em->getRepository('App:Campus')->find($id);
        $valeurCampus = "campus" . $id;
        $valeurCp = "cp" . $id;
        $changementNomCampus = $_POST[($valeurCampus)];
        $ville->setNom($changementNomCampus);
        $em->flush();
        return $this->redirect($_SERVER['HTTP_REFERER']);
    }

    /**
     * @Route("/delete_campus/{id}", name="delete_campus")
     */
    public function DeleteCampus(int $id): Response
    {
        $em = $this->getDoctrine()->getManager();
        $campus = $em->getRepository('App:Campus')->find($id);
        $em->remove($campus);
        $em->flush();

        return $this->redirect($_SERVER['HTTP_REFERER']);
    }

}
