<?php

namespace App\Form\Admin\Requested;

use App\Entity\Requested;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

// Form to allow admin to edit a request

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
