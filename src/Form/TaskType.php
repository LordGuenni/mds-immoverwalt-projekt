<?php

namespace App\Form;

use App\Entity\Issue;
use App\Entity\Property;
use App\Entity\RentalUnit;
use App\Entity\ServiceProvider;
use App\Entity\Task;
use App\Entity\User;
use App\Repository\ServiceProviderRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, ['label' => 'Titel'])
            ->add('description', TextareaType::class, ['label' => 'Beschreibung', 'required' => false])
            ->add('property', EntityType::class, ['label' => 'Objekt', 'class' => Property::class])
            ->add('unit', EntityType::class, ['label' => 'Mieteinheit', 'required' => false, 'placeholder' => '-', 'class' => RentalUnit::class])
            ->add('issue', EntityType::class, ['label' => 'zu Meldung', 'required' => false, 'placeholder' => '-', 'class' => Issue::class])
            ->add('responsible', EntityType::class, ['label' => 'verantwortlich', 'required' => false, 'class' => User::class])
            ->add('serviceProvider', EntityType::class, ['label' => 'Dienstleister', 'required' => false, 'placeholder' => '- intern -', 'query_builder' => function (ServiceProviderRepository $r) { return $r->createActiveQueryBuilder(); }, 'class' => ServiceProvider::class])
            ->add('contactPerson', TextType::class, ['label' => 'Ansprechpartner', 'required' => false])
            ->add('dueDate', DateType::class, ['label' => 'fällig am', 'required' => false, 'widget' => 'single_text'])
            ->add('scheduledAt', DateTimeType::class, ['label' => 'Termin vor Ort', 'required' => false, 'widget' => 'single_text'])
            ->add('priority', ChoiceType::class, ['label' => 'Priorität', 'choices' => Issue::PRIORITIES])
            ->add('status', ChoiceType::class, ['label' => 'Status', 'choices' => Task::STATUSES])
            ->add('notes', TextareaType::class, ['label' => 'Notizen', 'required' => false]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
        ]);
    }
}
