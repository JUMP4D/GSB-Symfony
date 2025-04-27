<?php

namespace App\Form;

use App\Entity\FicheFrais;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FicheComptableType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('visiteur', ChoiceType::class, [
                'choices' => $options['lesvisiteurs'],
                'choice_label' => function ($visiteur) {
                    return $visiteur->getNom() . ' ' . $visiteur->getPrenom();
                },
                'choice_value' => function ($visiteur) {
                    return $visiteur ? $visiteur->getId() : '';
                },
                'placeholder' => 'Sélectionnez un visiteur',
                'label' => 'Visiteur : ',
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Filtrer'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'lesvisiteurs' => [],
            'lesfiches' => []
        ]);
    }
}
