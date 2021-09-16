<?php

namespace App\Command;

use App\Entity\Campus;
use App\Entity\Etat;
use App\Entity\Participant;
use App\Repository\CampusRepository;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\This;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class InscriptionMultiUtilisateurCommand extends Command
{
    protected static $defaultName = 'app:import-command';
    private string $dataDirectory; // La route du fichier csv est importée grâce au fichier services.yaml
    private UserPasswordEncoderInterface $encoder;
    private EntityManagerInterface $manager;

    /**
     * ImportCommand constructor.
     * @param string $dataDirectory
     * @param UserPasswordEncoderInterface $encoder
     * @param EntityManagerInterface $manager
     */


    public function __construct(string $dataDirectory, UserPasswordEncoderInterface $encoder, EntityManagerInterface $manager)
    {
        parent::__construct(self::$defaultName);
        $this->dataDirectory = $dataDirectory;
        $this->encoder = $encoder;
        $this->manager = $manager;
    }


    protected function configure(): void
    {
        $this->setDescription('Import des Utilisateurs à partir d\'un fichier csv');

    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if (($handle = fopen($this->dataDirectory, "r")) !== false) {

            while (($line = fgetcsv($handle, 1000, ",")) !== false) {

                $em = $this->manager;
                $repo = $em->getRepository("App:Campus")
                    ->find($line[0]);

                $participant = (new Participant())
                    ->setEstRattacheA($repo)
                    ->setEmail($line[1])
                    ->setNom($line[2])
                    ->setPrenom($line[3])
                    ->setTelephone($line[4])
                    ->setPseudo($line[5]);

                $participant->setPassword($this->encoder->encodePassword($participant, 'Pa$$w0rd'));
                $participant->setAdministrateur(0);
                $participant->setActif(1);
                $participant->setRoles(["ROLE_USER"]);
                $this->manager->persist($participant);
            }
            fclose($handle);
        }
        $this->manager->flush();
        $io->success('Votre commande est un franc succès');
        return Command::SUCCESS;

    }
}
