<?php

namespace App\Form;

use App\Entity\Issue;
use App\Entity\Property;
use App\Entity\RentalUnit;
use App\Entity\Tenant;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class IssueType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('property', EntityType::class, ['label' => 'Objekt', 'class' => Property::class])
            ->add('unit', EntityType::class, ['label' => 'Mieteinheit', 'required' => false, 'placeholder' => '- keine / Allgemeinfläche -', 'class' => RentalUnit::class])
            ->add('tenant', EntityType::class, ['label' => 'Mieter (meldend)', 'required' => false, 'placeholder' => '- automatisch -', 'class' => Tenant::class])
            ->add('category', ChoiceType::class, ['label' => 'Kategorie', 'choices' => Issue::CATEGORIES])
            ->add('description', TextareaType::class, ['label' => 'Beschreibung', 'attr' => ['rows' => 5]])
            ->add('priority', ChoiceType::class, ['label' => 'Priorität', 'choices' => Issue::PRIORITIES])
            ->add('assignee', EntityType::class, ['label' => 'zuständig', 'required' => false, 'class' => User::class])
            ->add('attachments', TextType::class, ['label' => 'Anhänge (Dateinamen)', 'required' => false])
            ->add('internalNotes', TextareaType::class, ['label' => 'interne Notizen', 'required' => false]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Issue::class,
        ]);
    }
}
