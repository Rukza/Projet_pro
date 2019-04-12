<?php

namespace App\Form\Account;

use App\Form\ApplicationType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

// Form to change user password

class PasswordUpdateType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('oldPassword', PasswordType::class, $this->getConfiguration("Ancien mot de passe", "Donner votre mot de passe actuel"))
        ->add('newPassword', PasswordType::class, $this->getConfiguration("Nouveau mot de passe", "Donner votre nouveau mot de passe"))
        ->add('ConfirmPassword', PasswordType::class, $this->getConfiguration("Confirmation du mot de passe", "Confirmer votre nouveau mot de passe"));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
