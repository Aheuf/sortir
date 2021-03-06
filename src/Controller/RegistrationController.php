<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\RegistrationFormType;
use App\Repository\CampusRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
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
        $allCampus = $repository->findAll();

        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($this->getUser()) {
            throw new AccessDeniedHttpException();
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setAdministrateur(0);
            $user->setActif(1);
            $user->setRoles(["ROLE_USER"]);
                                            //gestion rattachement campus

            $campus = $repository->findOneBy(['id'=>$request->get('campus')]);
            $user->setEstRattacheA($campus);
                                                //gestion de l'image
            if ($form['avatar']->getData()){
                $nbRand = random_int(1000000000,2000000000);
                $avatar = $form['avatar']->getData();
                $nomSplit = explode('.',$avatar->getClientOriginalName());
                $nomSplit[0] = $user->getNom().$user->getPrenom().$nbRand;
                $nom = implode('.',$nomSplit);

                $folder = './img';
                $avatar->move($folder, $nom);
                $user->setAvatar($nom);
            }
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

            $this->addFlash('success','votre compte ?? ??t?? cr????');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(), 'allCampus'=>$allCampus
        ]);
    }
}
