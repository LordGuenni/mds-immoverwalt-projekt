<?php

namespace App\Form;

use App\Entity\Booking;
use App\Entity\Property;
use App\Entity\RentalUnit;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', ChoiceType::class, ['label' => 'Art', 'choices' => ['Einnahme' => 'INCOME', 'Ausgabe' => 'EXPENSE']])
            ->add('property', EntityType::class, ['label' => 'Objekt', 'class' => Property::class])
            ->add('unit', EntityType::class, ['label' => 'Mieteinheit', 'required' => false, 'placeholder' => '-', 'class' => RentalUnit::class])
            ->add('date', DateType::class, ['label' => 'Datum', 'widget' => 'single_text'])
            ->add('amount', MoneyType::class, ['label' => 'Betrag', 'currency' => 'EUR'])
            ->add('category', ChoiceType::class, ['label' => 'Kategorie', 'choices' => Booking::CATEGORIES])
            ->add('description', TextType::class, ['label' => 'Beschreibung', 'required' => false]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Booking::class,
        ]);
    }
}
