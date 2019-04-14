<?php

namespace App\Form\Mother\Weared;

use App\Entity\Weared;
use App\Form\WearedType;
use App\Entity\SerialNumber;
use App\Form\ApplicationType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

// Form to allow a mother account to add a wearer


class WearedAddType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
        $user = $options['user'];
        $builder
        ->add('firstName',TextType::class,$this->getConfiguration("Prénom", "Veuillez renseigner le prénom du porteur"))
        ->add('lastName',TextType::class,$this->getConfiguration("Nom", "Veuillez renseigner le nom du porteur"))
        ->add('adress',TextType::class,$this->getConfiguration("Adresse", "Veuillez renseigner l'adresse du porteur"))
        ->add('postalCode',TextType::class,$this->getConfiguration("Code postal", "Veuillez renseigner le code postal du porteur"))
        ->add('city',TextType::class,$this->getConfiguration("Ville", "Veuillez renseigner la ville du porteur"))
        ->add('wearWristlet', EntityType::class, [
            'class' => SerialNumber::class,
            'query_builder' => function (EntityRepository $er) use($user){
                return $er->createQueryBuilder('s')
                    ->setParameter('user', $user)
                    ->where('s.Mother = :user','s.attributedTo = 0');
            },
            'choice_label' => 'wristletTitle',
            'label' => 'Choisissez le nom du bracelet que vous souhaitez attribuer à la personne.',
            'placeholder' => 'Choisissez un nom',
        ])
        ->add('checkRgpd',CheckboxType::class,$this->getConfiguration(" Je certifie disposer des droits de traitement des données relatives à la personne concernée.", " ", array()));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        
            $resolver->setRequired(['user'])
          ;
          $resolver->setDefaults([
            'csrf_protection' => false,]);
    }
}
