<?php

namespace App\Controller;

use App\Repository\CampusRepository;
use App\Repository\VilleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormBuilderInterface;
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
    public function villes(VilleRepository $villeRepository): Response
    {
        $ville=$villeRepository->findAll();

        return $this->render('admin/villes.html.twig', [
            'ville' => $ville,
        ]);
    }


     /**
     * @Route("", name="campus")
     */
    public function campus(CampusRepository $campusRepository): Response
    {
        $campus=$campusRepository->findAll();

        return $this->render('admin/campus.html.twig', [
            'campus' => $campus,
        ]);
    }


}
