<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\Ville;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateSortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, ['attr'=>['class'=>'form-control'],
                'label' => 'Nom de la sortie : '])
            ->add('dateHeureDebut', DateTimeType::class, ['attr'=>['class'=>'form-control'],
                'label' => 'Date et heure de la sortie : ',
                'widget' => 'single_text'])
            ->add('dateLimiteInscription', DateType::class, ['attr'=>['class'=>'form-control'],
                'label' => 'Date limite d\'inscription : ',
                'widget' => 'single_text'])
            ->add('nbInscriptionMax', IntegerType::class, ['attr'=>['class'=>'form-control'],
                'label' => 'Nombre de places : '])
            ->add('duree', IntegerType::class, ['attr'=>['class'=>'form-control'],
                'label' => 'Durée : '])
            ->add('infoSortie',null,['attr'=>['class'=>'form-control'],'label' => 'Description : '])
            ->add('campus', TextType::class, [
                'label' => 'Campus : ',
                'attr'=>['disabled'=>true,'class'=>'form-control']])

            ->add('ville', EntityType::class, [
                'mapped' => false,
                'class' => Ville::class,
                'choice_label' => 'nom',
                'placeholder' => 'Ville',
                'attr'=>['class'=>'form-control'],
                'label' => 'Ville',
                'required' => false
            ])

            ->add('lieuSortie', EntityType::class, ['attr'=>['class'=>'form-control'],
                'label' => 'Lieu : ',
                //quelle est la classe à afficher ici ?
                'class' => Lieu::class,
                //quelle propriété utiliser pour les <option> dans la liste déroulante ?
                'choice_label' => 'nom'])

            ->add('save', SubmitType::class, ['label' => 'Enregistrer', 'attr'=>['class'=>'btn btn-outline-warning']])
            ->add('publish', SubmitType::class, ['label' => 'Publier une sortie','attr'=>['class'=>'btn btn-outline-success']])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
