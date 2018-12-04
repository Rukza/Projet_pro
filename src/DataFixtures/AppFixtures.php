<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;
    //ajout de la possibilité d'utiliser UserPasswordEncoderInterface
    public function __construct(UserPasswordEncoderInterface $encoder){
        $this->encoder = $encoder;
    }
    public function load(ObjectManager $manager)
    {

        $faker = Factory::create('FR-fr');

        //Ajout de faux user

        $users = [];

        for($i = 1; $i <= 10; $i++) {

            $user = new User();

            $pswd = $this->encoder->encodePassword($user, 'password');

            $user->setFirstName($faker->firstname)
                 ->setLastName($faker->lastName)
                 ->setEmail($faker->email)
                 ->setPswd($pswd);
                 
                 $manager->persist($user);
                 $users[] = $user;
        }


        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
    }
}
