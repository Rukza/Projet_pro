<?php

namespace App\Form;

use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Captcha\Bundle\CaptchaBundle\Form\Type\CaptchaType;


class ContactType extends ApplicationType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('firstName',TextType::class,$this->getConfiguration("Prénom", "Veuillez renseigner votre prénom"))
        ->add('lastName',TextType::class,$this->getConfiguration("Nom", "Veuillez renseigner votre nom"))
        ->add('email',EmailType::class,$this->getConfiguration("Email", "Veuillez renseigner votre email"))
        ->add('sujet',TextType::class,$this->getConfiguration("Sujet de votre demande", "Veuillez renseigner le sujet de votre demande"))
        ->add('message',TextareaType::class,$this->getConfiguration("Message", "Veuillez renseigner votre demande"))
        ->add('captchaCode', CaptchaType::class, array(
            'captchaConfig' => 'ExampleCaptcha',
            'label' => 'Veuillez valider le captcha'
          ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Contact::class
        ]);
    }
}
