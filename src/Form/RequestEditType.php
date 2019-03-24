<?php

namespace App\Form;

use App\Entity\Requested;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RequestEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('requestedMotherResponse')
            ->add('requestedAccepted')
            ->add('requestedRefused')
            ->add('requestedBanned')
            ->add('requestedFor')
            ->add('requestedBy')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Requested::class,
        ]);
    }
}
