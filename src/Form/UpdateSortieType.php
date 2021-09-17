<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Sortie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UpdateSortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom',null,['attr'=>['class'=>'form-control']])
            ->add('dateHeureDebut', DateTimeType::class,['attr'=>['class'=>'form-control'],'widget' => 'single_text'])
            ->add('duree',null,['attr'=>['class'=>'form-control']])
            ->add('dateLimiteInscription', DateType::class,['attr'=>['class'=>'form-control'],'widget' => 'single_text'])
            ->add('nbInscriptionMax',null,['attr'=>['class'=>'form-control']])
            ->add('infoSortie',null,['attr'=>['class'=>'form-control']])
            ->add('campus',TextType::class,['disabled'=>true, 'attr'=>['class'=>'form-control']])
            ->add('lieuSortie', EntityType::class, ['attr'=>['class'=>'form-control'],
                'label' => 'Lieu : ',
                'class' => Lieu::class,
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
