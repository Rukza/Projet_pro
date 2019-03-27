<?php

namespace App\Form\Registration;

use App\Entity\User;
use App\Form\ApplicationType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class RegistrationType extends ApplicationType
{
   
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName',TextType::class,$this->getConfiguration("Prénom", "Veuillez renseigner votre prénom"))
            ->add('lastName',TextType::class,$this->getConfiguration("Nom", "Veuillez renseigner votre nom"))
            ->add('email',EmailType::class,$this->getConfiguration("Email", "Veuillez renseigner votre email"))
            ->add('adress',TextType::class,$this->getConfiguration("Adresse", "Veuillez renseigner votre l'adresse"))
            ->add('postalCode',TextType::class,$this->getConfiguration("Code postal", "Veuillez renseigner votre code postal"))
            ->add('city',TextType::class,$this->getConfiguration("Ville", "Veuillez renseigner votre ville"))
            ->add('pswd',PasswordType::class,$this->getConfiguration("Mot de passe", "Veuillez renseigner votre mot de passe"))
            ->add('pswdConfirm',PasswordType::class,$this->getConfiguration("Confirmation de mot de passe", "Confirmer votre mot de passe"))
            ->add('checkConsent',CheckboxType::class,$this->getConfiguration("En soumettant ce formulaire, j'accepte que les informations saisie soient exploitées dans le cadre de la demande d'inscription sur le site et de cette application.", " ", array()))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
