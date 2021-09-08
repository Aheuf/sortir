<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Participant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UpdateAccountType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $niort = new Campus();
            $niort->setNom('Niort');
        $nantes = new Campus();
            $nantes->setNom('Nantes');
        $quimper = new Campus();
            $quimper->setNom('Quimper');
        $rennes = new Campus();
            $rennes->setNom('Rennes');

        $builder
            ->add('nom',null, ['attr'=>['class'=>'form-control']])
            ->add('prenom',null, ['attr'=>['class'=>'form-control']])
            ->add('pseudo',null, ['attr'=>['class'=>'form-control']])
            ->add('email',null, ['attr'=>['class'=>'form-control']])
            ->add('telephone',null, ['attr'=>['class'=>'form-control']])
            ->add('avatar',FileType::class,['attr'=>['class'=>'form-control']])

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }
}
