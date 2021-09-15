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
                'mapped'=>false,
                'class'=> Ville::class,
                'choice_label' => 'nom',
                'placeholder' => 'ville',
                'attr'=>['class'=>'form-control']
            ])

            ->add('lieuSortie', ChoiceType::class, ['attr'=>['class'=>'form-control'],
                'label' => 'Lieu : ',
                'placeholder' => 'Lieu (choisir une ville)'])

            ->add('save', SubmitType::class, ['label' => 'Enregistrer', 'attr'=>['class'=>'btn btn-outline-warning']])
            ->add('publish', SubmitType::class, ['label' => 'Publier une sortie','attr'=>['class'=>'btn btn-outline-success']])
        ;

        $formModifier = function(FormInterface $form, Ville $ville = null){
            $lieux = null === $ville ? [] : $ville->getLieus();

            $form->add('lieuSortie', EntityType::class, [
                'class'=>Lieu::class,
                'choices'=> $lieux,
                'choice_label'=>'nom',
                'placeholder' => 'Lieu (choisir une ville)',
                'label' => 'Lieu : ',
                'attr' => ['class'=>'form-control']
            ]);
        };

        $builder->get('ville')->addEventListener(
            FormEvents::POST_SUBMIT,
            function(FormEvent $event) use ($formModifier){
                $ville = $event->getForm()->getData();
                $formModifier($event->getForm()->getParent(), $ville);

            }
        );

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
