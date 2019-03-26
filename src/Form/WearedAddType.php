<?php

namespace App\Form;

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

class WearedAddType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
        $user = $options['user'];
        $builder
        ->add('firstName',TextType::class,$this->getConfiguration("Prénom", "Veuillez renseigner le prénom du porteur"))
        ->add('lastName',TextType::class,$this->getConfiguration("Nom", "Veuillez renseigner le nom du porteur"))
        ->add('adress',TextType::class,$this->getConfiguration("Adresse", "Veuillez renseigner l'adresse du porteur"))
        ->add('postalCode',TextType::class,$this->getConfiguration("Code postal", "Veuillez renseigner lcode postal du porteur"))
        ->add('city',TextType::class,$this->getConfiguration("Ville", "Veuillez renseigner la ville du porteur"))
        ->add('wearWristlet', EntityType::class, [
            'class' => SerialNumber::class,
            'query_builder' => function (EntityRepository $er) use($user){
                return $er->createQueryBuilder('s')
                    ->setParameter('user', $user)
                    ->where('s.Mother = :user')
                    ;
            },
            'choice_label' => 'wristletTitle',
            'label' => 'choisissez le nom du bracelet que vous souhaitez attribuer a la personne.',
            'placeholder' => 'Choisissez un nom',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        
            $resolver->setRequired(['user'])
          ;
    }
}
