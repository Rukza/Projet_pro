<?php

namespace App\Form\WristletLink;

use App\Entity\SerialNumber;
use App\Form\ApplicationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

//Form to link an account to a wristlet with serial number

class SerialNumberType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('serialWristlet', TextType::class, $this->getConfiguration("Numéro de série d'un bracelet", "Veuillez rentrer le numéro de série du bracelet"))
            ->add('checkConsent',CheckboxType::class,$this->getConfiguration("J'accepte que mes données personnelles puissent être transmises à l'ayant droit du bracelet auquel je souhaite faire une demande de liaison.", " ", array()))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SerialNumber::class,
        ]);
    }
}
