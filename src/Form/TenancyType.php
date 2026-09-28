<?php

namespace App\Form;

use App\Entity\Tenancy;
use App\Entity\Tenant;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TenancyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('tenants', EntityType::class, ['label' => 'Mieter', 'multiple' => true, 'expanded' => false, 'class' => Tenant::class])
            ->add('startDate', DateType::class, ['label' => 'Vertragsbeginn', 'widget' => 'single_text'])
            ->add('endDate', DateType::class, ['label' => 'Vertragsende (bei Befristung)', 'required' => false, 'widget' => 'single_text'])
            ->add('coldRent', MoneyType::class, ['label' => 'Kaltmiete', 'required' => false, 'currency' => 'EUR'])
            ->add('serviceChargeAdvance', MoneyType::class, ['label' => 'NK-Vorauszahlung', 'required' => false, 'currency' => 'EUR'])
            ->add('deposit', MoneyType::class, ['label' => 'Kaution', 'required' => false, 'currency' => 'EUR']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Tenancy::class,
        ]);
    }
}
