<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class RegistrationType extends AbstractType
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
            ->add('pswd',PasswordType::class,$this->getConfiguration("Mot de passe", "Veuillez renseigner votre mot de passe"))
            ->add('pswdConfirm',PasswordType::class,$this->getConfiguration("Confirmation de mot de passe", "Confirmer votre mot de passe"))
            ->add('checkConsent',CheckboxType::class,$this->getConfiguration("Afin de pouvoir terminer votre insciption veuillez donner votre accord de l'enregistrement de vos données personnel dans notre base de donnée", " "))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
