<?php

namespace App\Form;

use App\Entity\Handover;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HandoverType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', ChoiceType::class, ['label' => 'Art', 'choices' => ['Übergabe (Einzug)' => 'MOVE_IN', 'Rückgabe (Auszug)' => 'MOVE_OUT']])
            ->add('date', DateType::class, ['label' => 'Datum', 'widget' => 'single_text'])
            ->add('meterReadings', TextareaType::class, ['label' => 'Zählerstände', 'required' => false])
            ->add('keyCount', IntegerType::class, ['label' => 'Anzahl Schlüssel', 'required' => false])
            ->add('defects', TextareaType::class, ['label' => 'festgestellte Mängel', 'required' => false])
            ->add('remarks', TextareaType::class, ['label' => 'Bemerkungen', 'required' => false]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Handover::class,
        ]);
    }
}
