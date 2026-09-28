<?php

namespace App\Form;

use App\Entity\Prospect;
use App\Entity\RentalUnit;
use App\Entity\User;
use App\Entity\Viewing;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ViewingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('unit', EntityType::class, ['label' => 'Einheit', 'class' => RentalUnit::class])
            ->add('prospect', EntityType::class, ['label' => 'Interessent', 'class' => Prospect::class])
            ->add('dateTime', DateTimeType::class, ['label' => 'Termin', 'widget' => 'single_text'])
            ->add('employee', EntityType::class, ['label' => 'Mitarbeiter', 'required' => false, 'class' => User::class])
            ->add('remark', TextareaType::class, ['label' => 'Bemerkung', 'required' => false]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Viewing::class,
        ]);
    }
}
