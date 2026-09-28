<?php

namespace App\Form;

use App\Entity\Building;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BuildingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('buildingNumber', TextType::class, ['label' => 'Gebäudenummer'])
            ->add('name', TextType::class, ['label' => 'Bezeichnung'])
            ->add('address', TextType::class, ['label' => 'Anschrift', 'required' => false])
            ->add('floors', IntegerType::class, ['label' => 'Anzahl Etagen', 'required' => false])
            ->add('constructionYear', IntegerType::class, ['label' => 'Baujahr', 'required' => false]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Building::class,
        ]);
    }
}
