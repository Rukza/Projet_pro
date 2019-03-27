<?php

namespace App\Form\WristletLink;

use App\Entity\SerialNumber;
use App\Form\ApplicationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class SerialNumberRenameType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('wristletTitle', TextType::class, $this->getConfiguration("Nom du bracelet précédemment lier", "Veuillez donner un nom au bracelet"))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SerialNumber::class,
        ]);
    }
}
