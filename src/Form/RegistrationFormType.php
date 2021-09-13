<?php

namespace App\Form;

use App\Entity\Participant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {


        $builder
            ->add('nom',null, ['attr'=>['class'=>'form-control']])
            ->add('prenom',null, ['attr'=>['class'=>'form-control']])
            ->add('pseudo',null, ['attr'=>['class'=>'form-control']])
            ->add('avatar',FileType::class,['attr'=>['class'=>'form-control']])
            ->add('telephone',null, ['attr'=>['class'=>'form-control']])
            ->add('email',null, ['attr'=>['class'=>'form-control']])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => [
                    'attr' => ['autocomplete' => 'new-password', 'class'=>'form-control'],
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
                    'label' => 'Mot de passe',
                ],
                'second_options' => [
                    'attr' => ['autocomplete' => 'new-password', 'class'=>'form-control'],
                    'label' => 'Repeter mot de passe',
                ],
                'invalid_message' => 'Le mot de passe ne corresponds pas.',
                // Instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
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
