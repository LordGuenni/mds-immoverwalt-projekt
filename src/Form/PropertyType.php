<?php

namespace App\Form;

use App\Entity\Owner;
use App\Entity\Property;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PropertyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('propertyNumber', TextType::class, ['label' => 'Objektnummer'])
            ->add('name', TextType::class, ['label' => 'Bezeichnung'])
            ->add('street', TextType::class, ['label' => 'Straße'])
            ->add('houseNumber', TextType::class, ['label' => 'Hausnummer'])
            ->add('zip', TextType::class, ['label' => 'PLZ'])
            ->add('city', TextType::class, ['label' => 'Ort'])
            ->add('owner', EntityType::class, ['label' => 'Eigentümer', 'class' => Owner::class])
            ->add('constructionYear', IntegerType::class, ['label' => 'Baujahr', 'required' => false])
            ->add('managementStart', DateType::class, ['label' => 'Verwaltungsbeginn', 'required' => false, 'widget' => 'single_text'])
            ->add('status', ChoiceType::class, ['label' => 'Status', 'choices' => ['aktiv' => 'ACTIVE', 'Verwaltung beendet' => 'ENDED']]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Property::class,
        ]);
    }
}
