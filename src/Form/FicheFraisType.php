<?php

namespace App\Form;

use App\Entity\FicheFrais;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FicheFraisType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('mois', ChoiceType::class, [
                'choices' => $options['lesfiches'],
                'choice_label' => function (FicheFrais $ficheFrais) {
                    return $ficheFrais->getMois()->format('m-Y');
                },
                'choice_value' => function (?FicheFrais $ficheFrais) {
                    return $ficheFrais ? $ficheFrais->getId() : '';
                },
                'placeholder' => 'Sélectionnez un mois',
                'label' => 'Mois : ',
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Filtrer'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'lesfiches' => []
        ]);
    }
}
