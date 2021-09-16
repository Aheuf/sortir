<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Entity\Participant;
use App\Entity\Ville;
use App\Form\CampusType;
use App\Form\FilesType;
use App\Form\RegistrationFormType;
use App\Form\VilleType;
use App\Repository\CampusRepository;
use App\Repository\EtatRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use App\Repository\VilleRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Console\Input\ArrayInput;



/**
 * @Route("/admin", name="admin_")
 */
class AdminController extends AbstractController
{

    /**
     * @Route("/dashboard", name="dashboard")
     */
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

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
    public function UpdateVille(int $id): Response
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
    public function UpdateCampus(int $id): Response
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

    /**
     * @Route("/download",name="download")
     */
    public function download(): Response
    {
        // send the file contents and force the browser to download it
        return $this->file('dataFormat/fichierExemple.csv');
    }



    /**
     * @Route ("/users",name="users")
     */
    public function Users(KernelInterface $kernel,ParticipantRepository $repository, UserPasswordEncoderInterface $passwordEncoder, Request $request)
    {
        $users = $repository->findAll();
        $userRegisterAdmin = new Participant();
        $filesystem = new Filesystem();



        $formFiles = $this->createForm(FilesType::class);
        $formFiles->handleRequest($request);

        $formUser = $this->createForm(RegistrationFormType::class, $userRegisterAdmin);
        $formUser->add('estRattacheA', EntityType::class,
            ['label' => 'Campus',
                'class' => Campus::class,
                'choice_label' => 'nom',
                'required' => true
            ]);
        $formUser->remove('plainPassword');
        $formUser->remove('avatar');
        $formUser->handleRequest($request);

        if ($formFiles->isSubmitted() && $formFiles->isValid()) {
            /** @var UploadedFile $file */
            $file = $formFiles['telechargement']->getData();

            $folder='./data';
            $filesystem->remove(['symlink', $folder, 'participant.csv']);

            $file->move($folder,'participant.csv');

            $application = new Application($kernel);
            $application->setAutoExit(false);

            $input = new ArrayInput([
                'command' => 'app:import-command',
            ]);

            // You can use NullOutput() if you don't need the output
            $output = new BufferedOutput();
            $application->run($input, $output);

            // return the output, don't use if you used NullOutput()
            $content = $output->fetch();

            // return new Response(""), if you used NullOutput()

            return $this->redirect($_SERVER['HTTP_REFERER']);
        }


        if ($request->getMethod() == "POST") {
            if ($request->request->get("submitAction") == "Enregistrement") {

                $userRegisterAdmin->setAdministrateur(0);
                $userRegisterAdmin->setActif(1);
                $userRegisterAdmin->setPassword(
                    $passwordEncoder->encodePassword(
                        $userRegisterAdmin,
                        'Pa$$w0rd'));

                $userRegisterAdmin->setRoles(["ROLE_USER"]);

                $entityManager = $this->getDoctrine()->getManager();

                $entityManager->persist($userRegisterAdmin);
                $entityManager->flush();

                $this->addFlash('success', 'Le compte à été créé');
                return $this->redirect($_SERVER['HTTP_REFERER']);
            }
        }


        return $this->render('admin/users.html.twig', ['users' => $users, 'registrationForm' => $formUser->createView(), 'filesForm' => $formFiles->createView()]);
    }

    /**
     * @Route ("/delete_user/{id}",name="delete_user")
     */
    public function DeleteUser($id, ParticipantRepository $repository, EntityManagerInterface $entityManager)
    {
        $user = $repository->find($id);
        $entityManager->remove($user);
        $entityManager->flush();
        $this->addFlash('success', 'le compte à été supprimé');

        return $this->redirect($_SERVER['HTTP_REFERER']);
    }

    /**
     * @Route ("/ban_user/{id}",name="ban_user")
     */
    public function BanUser($id, ParticipantRepository $repository, EntityManagerInterface $entityManager)
    {
        $user = $repository->find($id);

        if ($user->getRoles() == ['ROLE_USER']) {
            $user->setRoles(['ROLE_BAN']);
            $this->addFlash('success', 'le compte à été banni');
        } else {
            $user->setRoles(['ROLE_USER']);
            $this->addFlash('success', 'le compte à été réactivé');
        }
        $entityManager->flush();

        return $this->redirect($_SERVER['HTTP_REFERER']);
    }

    /**
     * @Route ("/sorties",name="sorties")
     */
    public function Sorties(SortieRepository $sortieRepository, EtatRepository $etatRepository, Request $request)
    {
        $etatCancel = $etatRepository->findOneBy(['libelle' => 'Annulée']);

        $form = $this->createFormBuilder()
            ->add('query', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Entrez le nom de sortie'
                ]
            ])
            ->add('recherche', SubmitType::class, [
                'attr' => [

                    'class' => 'btn btn-outline-secondary']
            ])
            ->getForm();

        if (isset($request->request->get('form')['query'])) {
            $query = $request->request->get('form')['query'];
            $sortie = $sortieRepository->findSortieAdmin($query);

        } else {
            $sortie = $sortieRepository->findAll();

        }
        return $this->render('admin/sorties.html.twig', [
            'sorties' => $sortie, 'etatCancel' => $etatCancel, 'formSortieEtat' => $form->createView()
        ]);
    }

    /**
     * @Route("/cancel_sortie/{id}", name="cancel_sortie")
     */
    public function cancel(int $id, SortieRepository $sortieRepository, EtatRepository $repository, EntityManagerInterface $entityManager): Response
    {
        //récupère cette sortie en fonction de l'id présent dans l'URL
        $sortie = $sortieRepository->find($id);
        $etat = $repository->findOneBy(['libelle' => 'Annulée']);
        $sortie->setEtat($etat);
        $entityManager->flush();

        //s'il n'existe pas en bdd, on déclenche une erreur 404
        if (!$sortie) {
            throw $this->createNotFoundException('Cette sortie n\'existe pas. Désolé!');
        }
        $this->addFlash('danger', 'la sortie à été supprimée');
        return $this->redirect($_SERVER['HTTP_REFERER']);
    }

}
