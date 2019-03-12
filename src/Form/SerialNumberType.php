<?php

namespace App\Form;

use App\Entity\SerialNumber;
use App\Form\ApplicationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class SerialNumberType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('serialWristlet', NumberType::class, $this->getConfiguration("Numéro de série d'un bracelet", "Veuillez rentrer le numéro de série du bracelet"))
            ->add('wristletTitle',TextType::class, $this->getConfiguration("Nom du bracelet", "Veuillez nomer le bracelet"))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SerialNumber::class,
        ]);
    }
}
