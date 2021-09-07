<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Entity\Participant;
use App\Form\RegistrationFormType;
use App\Repository\CampusRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, CampusRepository $repository): Response
    {
        $user = new Participant();
        $campus = new Campus();

        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setAdministrateur(0);
            $user->setActif(1);
                                            //gestion rattachement campus
            $campus = $repository->findOneBy(['nom'=>strtoupper($form['estRattacheA']->getData()->getNom())]);
            $user->setEstRattacheA($campus);
                                                //gestion de l'image
            $nbRand = random_int(1000000000,2000000000);
            $avatar = $form['avatar']->getData();
            $nomSplit = explode('.',$avatar->getClientOriginalName());
            $nomSplit[0] = $user->getNom().$user->getPrenom().$nbRand;
            $nom = implode('.',$nomSplit);

            $folder = './img';
            $avatar->move($folder, $nom);
            $user->setAvatar($nom);
                                                // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            $this->addFlash('sucess','votre compte à été créé');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
