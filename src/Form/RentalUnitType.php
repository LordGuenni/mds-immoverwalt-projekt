<?php

namespace App\Form;

use App\Entity\Building;
use App\Entity\RentalUnit;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RentalUnitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('unitNumber', TextType::class, ['label' => 'Einheit'])
            ->add('building', EntityType::class, ['label' => 'Gebäude', 'class' => Building::class])
            ->add('type', ChoiceType::class, ['label' => 'Typ', 'choices' => ['Wohnung' => 'WHG', 'Gewerbe' => 'GEW']])
            ->add('floor', IntegerType::class, ['label' => 'Etage', 'required' => false])
            ->add('area', NumberType::class, ['label' => 'Fläche (m²)', 'required' => false, 'scale' => 2])
            ->add('coldRent', MoneyType::class, ['label' => 'Kaltmiete', 'required' => false, 'currency' => 'EUR'])
            ->add('serviceChargeAdvance', MoneyType::class, ['label' => 'NK-Vorauszahlung', 'required' => false, 'currency' => 'EUR'])
            ->add('status', ChoiceType::class, ['label' => 'Status', 'choices' => ['frei' => 'AVAILABLE', 'reserviert' => 'RESERVED', 'vermietet' => 'RENTED', 'gekündigt' => 'NOTICE_GIVEN', 'Renovierung' => 'RENOVATION', 'gesperrt' => 'BLOCKED']])
            ->add('showInList', CheckboxType::class, ['label' => 'in Liste anzeigen', 'required' => false]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => RentalUnit::class,
        ]);
    }
}
