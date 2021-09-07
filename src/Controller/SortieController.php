<?php

namespace App\Controller;

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
}
