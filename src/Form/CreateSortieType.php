<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Sortie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
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
            ->add('dateHeureDebut', DateType::class, ['widget' => 'single_text'])
            ->add('duree', IntegerType::class)
            ->add('dateLimiteInscription', DateType::class, ['widget' => 'single_text'])
            ->add('nbInscriptionMax')
            ->add('infoSortie')

            ->add('campus', TextType::class, ['attr'=>['disabled'=>true]])

            ->add('lieuSortie', EntityType::class, [
                'label' => 'Lieu',
                //quelle est la classe à afficher ici ?
                'class' => Lieu::class,
                //quelle propriété utiliser pour les <option> dans la liste déroulante ?
                'choice_label' => 'nom'])

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
