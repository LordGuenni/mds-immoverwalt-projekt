<?php

namespace App\Form;

use App\Entity\Prospect;
use App\Entity\RentalUnit;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProspectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, ['label' => 'Name'])
            ->add('email', EmailType::class, ['label' => 'E-Mail', 'required' => false])
            ->add('phone', TextType::class, ['label' => 'Telefon', 'required' => false])
            ->add('unit', EntityType::class, ['label' => 'gewünschte Einheit', 'required' => false, 'class' => RentalUnit::class])
            ->add('requestDate', DateType::class, ['label' => 'Anfragedatum', 'widget' => 'single_text'])
            ->add('status', ChoiceType::class, ['label' => 'Status', 'choices' => Prospect::STATUSES])
            ->add('remark', TextareaType::class, ['label' => 'Bemerkung', 'required' => false]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Prospect::class,
        ]);
    }
}
