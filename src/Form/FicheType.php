<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FicheType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('forfaitEtape', IntegerType::class, [
                'label' => 'Forfait Etape',
                'required' => true,
            ])
            ->add('fraisKilometrique', IntegerType::class, [
                'label' => 'Frais Kilométrique',
                'required' => true,
            ])
            ->add('nuiteeHotel', IntegerType::class, [
                'label' => 'Nuitée Hôtel',
                'required' => true,
            ])
            ->add('repasRestaurant', IntegerType::class, [
                'label' => 'Repas Restaurant',
                'required' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
