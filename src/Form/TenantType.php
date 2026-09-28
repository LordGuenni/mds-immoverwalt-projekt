<?php

namespace App\Form;

use App\Entity\Tenant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TenantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('salutation', ChoiceType::class, ['label' => 'Anrede', 'required' => false, 'choices' => ['Frau' => 'Frau', 'Herr' => 'Herr', 'Divers' => 'Divers']])
            ->add('firstName', TextType::class, ['label' => 'Vorname'])
            ->add('lastName', TextType::class, ['label' => 'Nachname'])
            ->add('email', EmailType::class, ['label' => 'E-Mail', 'required' => false])
            ->add('phone', TextType::class, ['label' => 'Telefon', 'required' => false])
            ->add('birthDate', DateType::class, ['label' => 'Geburtsdatum', 'required' => false, 'widget' => 'single_text'])
            ->add('previousAddress', TextareaType::class, ['label' => 'Voranschrift', 'required' => false])
            ->add('notes', TextareaType::class, ['label' => 'Notizen', 'required' => false]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Tenant::class,
        ]);
    }
}
