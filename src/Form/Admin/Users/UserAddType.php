<?php

namespace App\Form\Admin\Users;

use App\Entity\User;
use App\Form\ApplicationType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UserAddType extends ApplicationType
{
   
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName',TextType::class,$this->getConfiguration("Prénom", "Veuillez renseigner votre prénom"))
            ->add('lastName',TextType::class,$this->getConfiguration("Nom", "Veuillez renseigner votre nom"))
            ->add('email',EmailType::class,$this->getConfiguration("Email", "Veuillez renseigner votre email"))
            ->add('pswd',PasswordType::class,$this->getConfiguration("Mot de passe", "Veuillez renseigner votre mot de passe"))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
