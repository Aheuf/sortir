<?php

namespace App\Form;

use App\Entity\Sortie;
use Doctrine\DBAL\Types\StringType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateSortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom')
            ->add('dateHeureDebut')
            ->add('duree') //, IntegerType::class)
            ->add('dateLimiteInscription')
            ->add('nbInscriptionMax')
            ->add('infoSortie')
            //->add('participants')
            //->add('participant')
            //->add('campus', ChoiceType::class, [
            //    'choices' => [
            //        'NIORT' => 'NIORT',
            //        'QUIMPER' => 'QUIMPER',
            //        'RENNES' => 'RENNES'
            //        'NANTES' => 'NANTES')
            //->add('etat')

            //->add('lieuSortie', TextType::class)

            ->add('save', SubmitType::class, ['label' => 'Enregistrer'])
            ->add('publish', SubmitType::class, ['label' => 'Publier une sortie'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
