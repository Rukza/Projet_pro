<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Role;
use App\Entity\User;
use App\Entity\Weared;
use App\Entity\Requested;
use App\Entity\SerialNumber;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;


class AppFixtures extends Fixture
{
    
    private $encoder;
    //création constructeur pr inject dependance
    public function __construct(UserPasswordEncoderInterface $encoder, TokenGeneratorInterface $tokenGenerator){
        $this->encoder = $encoder;
        $this->tokenGenerator = $tokenGenerator;
        
    }

    public function load(ObjectManager $manager)
    {

        $faker = Factory::create('fr_FR');


        //Craft of one admin

        $adminRole = new Role();
        $adminRole->setTitle('ROLE_ADMIN');
        $manager->persist($adminRole);

        $motherRole = new Role();
        $motherRole->setTitle('ROLE_MOTHER');
        $manager->persist($motherRole);

        $childrenRole = new Role();
        $childrenRole->setTitle('CHILD');
        $manager->persist($childrenRole);

        $adminUser = new User();

        $hash = $this->encoder->encodePassword($adminUser, 'Taz291283');

        $adminUser->setFirstName('Casanova')
                  ->setLastName('Julien')
                  ->setAdress('4 rue du luxembourg')
                  ->setPostalCode('30140')
                  ->setCity('Anduze')
                  ->setEmail('Rukza@orange.fr')
                  ->setPswd($hash)
                  ->setActive('true')
                  ->addUserRole($adminRole);


        $manager->persist($adminUser);


        //Craft of users
        
        $users = [];
        
        $token = $this->tokenGenerator->generateToken();  

        for($i = 1;$i <= 30; $i++){
            $user = new User();


            $hash = $this->encoder->encodePassword($user, 'Taz291283');
            

            $user->setFirstName($faker->firstname)
                 ->setLastName($faker->lastname)
                 ->setAdress($faker->streetAddress)
                 ->setPostalCode($faker->postcode)
                 ->setCity($faker->city)
                 ->setEmail($faker->email)
                 ->setPswd($hash)
                 ->setActive($faker->boolean($chanceOfGettingTrue = 80));
                 if( $user->getActive() == 0){
                     $user->setPasswordRequestedAt(new \Datetime())
                          ->setToken($token);                 
                 }
            $manager->persist($user);
            $users[] = $user;
        }


        //Craft of users wearer

        $wearers = [];

        for($i = 1;$i <= 15; $i++){
        $wearer = new Weared();
        
        $wearer->setFirstName($faker->firstname)
                ->setLastName($faker->lastname)
                ->setAdress($faker->streetAddress)
                ->setPostalCode($faker->postcode)
                ->setCity($faker->city);
                $manager->persist($wearer);
                $wearers[] = $wearer;
        }



        //Craft of a random serial number
       

            $longueur = 8;
            $listeCar = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $chaine = '';
            $max = mb_strlen($listeCar, '8bit') - 1;
            for ($i = 0; $i < $longueur; ++$i) {
            $chaine .= $listeCar[random_int(0, $max)];
            }
            
            $newNumbers = [];

        for($i = 1;$i <= 25; $i++){
            $newNumber = new SerialNumber;
                
            $user = $users[mt_rand(0,count($users)-1)];
            $weared = $wearers[mt_rand(0,count($wearers)-1)];

            
            $newNumber->setSerialWristlet($chaine)
                      ->setWristletTitle($faker->lastname)
                      ->setActiveSerial(1)
                      ->setMother($user)
                      ->setAttributedTo($faker->boolean($chanceOfGettingTrue = 80));
                $user->addUserRole($motherRole);
            if($newNumber->getAttributedTo() == 1){
               $newNumber->setWearedBy($weared);
            }            
                $manager->persist($newNumber);
                $newNumbers[] = $newNumber;
            
        }

        //Craft of request

            for($i = 1;$i <= 25; $i++){

                $user = $users[mt_rand(0,count($users)-1)];
                $Number = $newNumbers[mt_rand(0,count($newNumbers)-1)];
                

                    $requested = new Requested();
                    $requested->setRequestedFor($Number)
                              ->setRequestedBy($user)
                              ->setRequestedAccepted($faker->boolean($chanceOfGettingTrue = 65));
                                if($requested->getRequestedAccepted() == 0){
                                    $requested->setRequestedBanned($faker->boolean($chanceOfGettingTrue = 50));

                                    if($requested->getRequestedBanned() == 0){
                                        $requested->setRequestedRefused(1)
                                                  ->setRequestedAt(new \Datetime())
                                                  ->setRequestedToken($token);


                                    }
                                    
                                }
            
                                
                    $manager->persist($requested);
                    }

                    // Dell all wearer who have not a wear

                    

                    foreach($wearers as $value){
                        
                        if($wearer->getWearWristlet() == null){
                            
                            $manager-remove($wearer);
                        }
                    }

        $manager->flush();
    }

}
