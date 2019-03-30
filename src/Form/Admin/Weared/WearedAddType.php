<?php

namespace App\Form\Admin\Weared;

use App\Entity\Weared;
use App\Form\ApplicationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

//Form to allow admin to add wearer

class WearedAddType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('firstName',TextType::class,$this->getConfiguration("Prénom", "Veuillez renseigner le prénom du porteur"))
        ->add('lastName',TextType::class,$this->getConfiguration("Nom", "Veuillez renseigner le nom du porteur"))
        ->add('adress',TextType::class,$this->getConfiguration("Adresse", "Veuillez renseigner l'adresse du porteur"))
        ->add('postalCode',TextType::class,$this->getConfiguration("Code postal", "Veuillez renseigner le code postal du porteur"))
        ->add('city',TextType::class,$this->getConfiguration("Ville", "Veuillez renseigner la ville du porteur"))
        ->add('wearWristlet')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Weared::class,
        ]);
    }
}
