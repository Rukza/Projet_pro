<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(
 * fields={"email"},
 * message="Adresse mail incompatible vueillez en rentrer une autre"
 * )
 */
class User implements UserInterface 
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     *  @Assert\NotBlank(message="Merci de renseigner votre prénom")
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Merci de renseigner votre nom de famille")
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Email(message="Veuillez renseigner un email valide")
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min=8,minMessage="Votre mot de passe doit faire au moins 8 caractères")
     * @Assert\Regex("/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]).{8,}$/", message="Votre mot de passe doit contenir au minimum une majuscule, une minuscule et un chiffre")
     */
    private $pswd;
    /**
     * @Assert\EqualTo(propertyPath="pswd", message="Vos mots de passes ne sont pas identiques")
     *
     * 
     */
    public $pswdConfirm;


        /**
         * 
         * @Assert\NotBlank
         * 
         */
    public $checkConsent;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPswd(): ?string
    {
        return $this->pswd;
    }

    public function setPswd(string $pswd): self
    {
        $this->pswd = $pswd;

        return $this;
    }


        // Returns the roles granted to the user.
    public function getRoles(){
            return ['ROLE_USER'];
         }

        // Returns the password used to authenticate the user.
         public function getPassword(){
            return $this->pswd;
        }

        //Returns the salt that was originally used to encode the password.
        //Already do by bcrypt
        public function getSalt(){}

            
        //Returns the username used to authenticate the user.
        public function getUsername(){
            return $this->email;
        }

        //Removes sensitive data from the user.
        public function eraseCredentials(){

        }
}
