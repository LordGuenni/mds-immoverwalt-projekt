<?php

namespace App\Form;

use App\Entity\Appointment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AppointmentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, ['label' => 'Titel'])
            ->add('type', ChoiceType::class, ['label' => 'Art', 'choices' => Appointment::TYPES])
            ->add('startsAt', DateTimeType::class, ['label' => 'Beginn', 'widget' => 'single_text'])
            ->add('endsAt', DateTimeType::class, ['label' => 'Ende', 'required' => false, 'widget' => 'single_text'])
            ->add('refType', ChoiceType::class, ['label' => 'bezieht sich auf', 'choices' => Appointment::REF_TYPES])
            ->add('refId', IntegerType::class, ['label' => 'ID des Bezugs', 'required' => false])
            ->add('description', TextareaType::class, ['label' => 'Beschreibung', 'required' => false]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Appointment::class,
        ]);
    }
}
