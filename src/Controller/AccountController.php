<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\UpdateAccountType;
use App\Repository\CampusRepository;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/account",name="account")
 */
class AccountController extends AbstractController
{
    /**
     * @Route("/{id}",name="list")
     */
    public function show($id, ParticipantRepository $repository, CampusRepository $campusRepository){
        $user = $repository->find($id);

        if (!$user){
            throw $this->createNotFoundException();
        }

        $campus = $campusRepository->find($user->getEstRattacheA());
        return $this->render('account/show.html.twig',['user'=>$user, 'campus'=>$campus]);
    }

    /**
     * @Route ("/update/{id}",name="update")
     */
    public function update($id, ParticipantRepository $repository,Request $request,CampusRepository $campusRepository,
                           EntityManagerInterface $entityManager){
        $user = new Participant();

        $form =$this->createForm(UpdateAccountType::class,$user);
        $form->handleRequest($request);

        $user = $repository->find($id);

        $allCampus = $campusRepository->findAll();

        if ($this->getUser() != $user) {
            throw new AccessDeniedHttpException();
        }

        if ($form->isSubmitted() && $form-> isValid() || $form['email']->getData()==$user->getEmail()) {

                                                //gestion rattachement campus
            $campus = $campusRepository->findOneBy(['id'=>$request->get('campus')]);
            $user->setEstRattacheA($campus);

            $user->setNom($form['nom']->getData());
            $user->setPrenom($form['prenom']->getData());
            $user->setPseudo($form['pseudo']->getData());
            $user->setEmail($form['email']->getData());
            $user->setTelephone($form['telephone']->getData());

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

            $entityManager->flush();
            $this->addFlash('success','votre compte à été modifié');
            return $this->redirectToRoute('app_account_show',['id' => $id]);
        }

        return $this->render('account/update.html.twig',['user'=>$user, 'UpdateAccountForm' => $form->createView(), 'allCampus' => $allCampus]);
    }
}