<?php

namespace App\Form\Mother\Response;

use App\Entity\Requested;
use App\Form\ApplicationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

// Form after mail 
//to allow a mother account to choice if they want to accept,refuse,ban a request to link at is wristlet


class RequestedType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('requestedMotherResponse')
            ->add('accepter',SubmitType::class, array('label' => 'Accepter'))
            ->add('refuser',SubmitType::class, array('label' => 'Refuser'))
            ->add('bannir',SubmitType::class, array('label' => 'Bannir'))
            ;
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Requested::class,
        ]);
    }
}
