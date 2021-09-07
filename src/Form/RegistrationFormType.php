<?php

namespace App\Form;

use App\Entity\Participant;
use App\Entity\Campus;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
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
            ->add('avatar',FileType::class,['attr'=>['class'=>'form-control']])
            ->add('estRattacheA',ChoiceType::class,['attr'=>['class'=>'form-select'],
                'choices'=>[$niort,
                            $nantes,
                            $quimper,
                            $rennes],
                'choice_value'=> 'nom',
                'choice_label'=>function(?Campus $campus){return $campus->getNom();}])
            ->add('telephone',null, ['attr'=>['class'=>'form-control']])
            ->add('email',null, ['attr'=>['class'=>'form-control']])
            ->add('plainPassword', PasswordType::class, ['label'=>'mot de passe',
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password','class'=>'form-control'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }
}
