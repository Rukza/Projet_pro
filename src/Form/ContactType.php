<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ContactType extends AbstractType
{

    private function getConfiguration($label, $placeholder){
        return [
            'label' => $label,
            'attr'  =>[
                'placeholder' => $placeholder
            ]
            ];
    }
    /**
         * Configuration de base d'un champ
         *
         * @param string $label
         * @param string $placeholder
         * @param array $options
         * 
         * @return array
         */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('firstName',TextType::class,$this->getConfiguration("Prénom", "Veuillez renseigner votre prénom"))
        ->add('lastName',TextType::class,$this->getConfiguration("Nom", "Veuillez renseigner votre nom"))
        ->add('email',EmailType::class,$this->getConfiguration("Email", "Veuillez renseigner votre email"))
        ->add('sujet',TextType::class,$this->getConfiguration("Sujet de votre demande", "Veuillez renseigner le sujet de votre demande"))
        ->add('message',TextareaType::class,$this->getConfiguration("Message", "Veuillez renseigner votre demande"))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
