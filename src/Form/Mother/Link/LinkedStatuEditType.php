<?php

namespace App\Form\Mother\Link;

use App\Entity\Requested;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

// Form to allow a mother account to manage request status

class LinkedStatuEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
       
        ->add('accepter',SubmitType::class, array(
            'label' => 'Accepter',
            'attr' => array(
                'class' => 'btn btn-lg btn-success',
            )
            ))
        ->add('refuser',SubmitType::class, array(
            'label' => 'Refuser',
            'attr' => array(
                'class' => 'btn btn-lg btn-warning',
            )
            ))
        ->add('bannir',SubmitType::class, array(
            'label' => 'Bloquer',
            'attr' => array(
                'class' => 'btn btn-lg btn-danger',
            )
            ))
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
