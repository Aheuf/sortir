<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Entity\Participant;
use App\Form\UpdateAccountType;
use App\Repository\CampusRepository;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/account","account")
 */
class AccountController extends AbstractController
{
    /**
     * @Route("/{id}","")
     */
    public function show($id, ParticipantRepository $repository, CampusRepository $campusRepository){
        $user = $repository->find($id);
        $campus = $campusRepository->find($user->getEstRattacheA());
        return $this->render('account/show.html.twig',['user'=>$user, 'campus'=>$campus]);
    }

    /**
     * @Route ("/update/{id}","")
     */
    public function update($id, ParticipantRepository $repository,Request $request,CampusRepository $campusRepository, EntityManagerInterface $entityManager){
        $user = new Participant();
        $form =$this->createForm(UpdateAccountType::class,$user);
        $user = $repository->find($id);
        $form->handleRequest($request);

        if ($form->isSubmitted()){
                                                //gestion rattachement campus
            $campus = $campusRepository->findOneBy(['nom'=>strtoupper($form['estRattacheA']->getData()->getNom())]);
            $user->setEstRattacheA($campus);

            $user->setNom($form['nom']->getData());
            $user->setPrenom($form['prenom']->getData());
            $user->setEmail($form['email']->getData());
            $user->setTelephone($form['telephone']->getData());

                                                        //gestion de l'image
            $nbRand = random_int(1000000000,2000000000);
            $avatar = $form['avatar']->getData();
            $nomSplit = explode('.',$avatar->getClientOriginalName());
            $nomSplit[0] = $user->getNom().$user->getPrenom().$nbRand;
            $nom = implode('.',$nomSplit);

            $folder = './img';
            $avatar->move($folder, $nom);
            $user->setAvatar($nom);

            $entityManager->flush();
            $this->addFlash('sucess','votre compte à été modifié');
            return $this->redirectToRoute('app_account_show',['id' => $id]);
        }

        return $this->render('account/update.html.twig',['user'=>$user, 'UpdateAccountForm' => $form->createView()]);
    }
}