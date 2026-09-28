<?php

namespace App\Form;

use App\Entity\ServiceProvider;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ServiceProviderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('company', TextType::class, ['label' => 'Firma'])
            ->add('trade', ChoiceType::class, ['label' => 'Gewerk', 'choices' => ServiceProvider::TRADES])
            ->add('contactPerson', TextType::class, ['label' => 'Ansprechpartner', 'required' => false])
            ->add('phone', TextType::class, ['label' => 'Telefon', 'required' => false])
            ->add('email', EmailType::class, ['label' => 'E-Mail', 'required' => false])
            ->add('address', TextType::class, ['label' => 'Adresse', 'required' => false])
            ->add('status', ChoiceType::class, ['label' => 'Status', 'choices' => ['aktiv' => 'ACTIVE', 'inaktiv' => 'INACTIVE']]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ServiceProvider::class,
        ]);
    }
}
