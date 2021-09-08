<?php


namespace App\Form;

use \Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class rechercheType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('campus', ChoiceType::class, [
                'choices' => [
                    'Nantes' => 'NANTES',
                    'Niort' => 'NIORT',
                    'Rennes' => 'RENNES',
                    'Quimper' => 'QUIMPER',
                ],
                'label' => 'Campus :',
                'attr' => [
                    'class' => 'col-6'
                ]
            ])
            ->add('nom', TextType::class, [
                'label' => 'Le nom contient : ',
                'attr' => [
                    'class' => 'col-6'
                ]
            ])
            ->add('debut', DateType::class, [
                'label' => 'Entre : ',
                'attr' => [
                    'class' => 'col-6'
                ]
            ])
            ->add('fin', DateType::class, [
                'label' => ' et : ',
                'attr' => [
                    'class' => 'col-6'
                ]
            ])
            ->add('organisateur', CheckboxType::class, [
                'label' => 'Sorties dont je suis l\'organisateur',
                'attr' => [
                    'class' => 'form-check-input'
                ]
            ])
            ->add('inscrit', CheckboxType::class, [
                'label' => 'Sorties auxquelles je suis inscrit/e',
                'attr' => [
                    'class' => 'form-check-input'
                ]
            ])->add('noninscrit', CheckboxType::class, [
                'label' => 'Sorties auxquelles je ne suis pas inscrit/e',
                'attr' => [
                    'class' => 'form-check-input'
                ]
            ])->add('passees', CheckboxType::class, [
                'label' => 'Sorties passées',
                'attr' => [
                    'class' => 'form-check-input'
                ]
            ]);

    }
}