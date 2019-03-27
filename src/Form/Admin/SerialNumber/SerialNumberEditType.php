<?php

namespace App\Form\Admin\SerialNumber;

use App\Entity\SerialNumber;
use App\Form\ApplicationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SerialNumberEditType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('serialWristlet')
            ->add('wristletTitle')
            ->add('active')
            ->add('Mother')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SerialNumber::class,
        ]);
    }
}
