<?php

namespace App\Form;

use App\Entity\Owner;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OwnerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('ownerNumber', TextType::class, ['label' => 'Eigentümernummer'])
            ->add('name', TextType::class, ['label' => 'Name / Firma'])
            ->add('address', TextareaType::class, ['label' => 'Anschrift', 'required' => false])
            ->add('contactPerson', TextType::class, ['label' => 'Ansprechpartner', 'required' => false])
            ->add('email', EmailType::class, ['label' => 'E-Mail', 'required' => false])
            ->add('phone', TextType::class, ['label' => 'Telefon', 'required' => false])
            ->add('status', ChoiceType::class, ['label' => 'Status', 'choices' => ['aktiv' => 'ACTIVE', 'inaktiv' => 'INACTIVE']]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Owner::class,
        ]);
    }
}
