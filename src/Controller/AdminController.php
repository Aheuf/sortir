<?php

namespace App\Controller;

use App\Entity\Ville;
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
     * @Route("", name="villes")
     */
    public function villes(VilleRepository $villeRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createFormBuilder()
            ->add('query', TextType::class, [
                'label' => 'Le nom contient ',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Entrez un mot-clé'
                ]
            ])
            ->add('recherche', SubmitType::class, [
                'attr' => ['class' => 'btn btn-primary']
            ])
            ->getForm();


        $ville = $villeRepository->findBy([], ['nom' => 'DESC']);
        $createVille = new Ville();
        $VilleForm = $this->createForm(VilleType::class, $createVille);
        $VilleForm->handleRequest($request);

        if ($request->request->get('form')['query'] !== null) {
            $query = $request->request->get('form')['query'];
            $ville = $villeRepository->findVillesByName($query);

            return $this->render('admin/villes.html.twig', [
                'villes' => $ville, 'VilleForm' => $VilleForm->createView(), 'form' => $form->createView()
            ]);
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
     * @Route("", name="campus")
     */
    public function campus(CampusRepository $campusRepository): Response
    {
        $campus = $campusRepository->findAll();

        return $this->render('admin/campus.html.twig', [
            'campus' => $campus,
        ]);
    }


}
