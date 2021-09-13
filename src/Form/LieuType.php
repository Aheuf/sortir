<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Ville;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LieuType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom')
            ->add('rue')
            ->add('latitude')
            ->add('longitude')
            ->add('LieuDansVille',EntityType::class,
            ['label'=>'Ville',
                'class'=>Ville::class,
                'choice_label'=>'nom',
                'required'=>true])
            ->add('enregistrementLieu', SubmitType::class,[
                'label'=>'Enregistrer',
                'class'=>'btn btn-primary'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Lieu::class,
        ]);
    }
}
