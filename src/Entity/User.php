<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(
 * fields={"email"},
 * message="Adresse mail incompatible veuillez en rentrer une autre"
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
    * @Assert\NotBlank(groups={"registration"})
    * 
    */
    public $checkConsent;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var \DateTime
     * 
     */
    private $passwordRequestedAt;
 
    /**
     * @var string
     * 
     * @ORM\Column(type="string", length=255, nullable=true)
     */

    private $token;

    /**
     * @var string
     * 
     * @ORM\Column(type="string", length=255, nullable=true)
     */

    private $tokenChildRequest;


    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Role", mappedBy="users")
     */
    private $userRoles;

    /**
	 * @ORM\Column(type="boolean")
	 */
    private $active;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\SerialNumber", mappedBy="userNumber")
     */
    private $userSerialNumber;

    public function __construct()
    {
        $this->userRoles = new ArrayCollection();
        $this->active = false;
        $this->userSerialNumber = new ArrayCollection();
    }

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

    //Mise en place d'une fonction pour avoir le nom complet et simplifier le code
    public function getFullName(){
    return "{$this->firstName} {$this->lastName}";
    }



    public function getPasswordRequestedAt()
    {
        return $this->passwordRequestedAt;
    }

    public function setPasswordRequestedAt($passwordRequestedAt)
    {
        $this->passwordRequestedAt = $passwordRequestedAt;
        return $this;
    }


    public function getToken()
    {
        return $this->token;
    }

    public function setToken($token)
    {
        $this->token = $token;
        return $this;
    }
    public function getTokenChildRequest()
    {
        return $this->tokenChildRequest;
    }

    public function setTokenChildRequest($tokenChildRequest)
    {
        $this->tokenChildRequest = $tokenChildRequest;
        return $this;
    }

    public function getRoles()//renvois la liste des roles sous chaine de caractère
    {
       $roles = $this->userRoles->map(function($role){
           //map boucle sur tout les elements du array collection et renvois en tbl avec les elements transformes
           return $role->getTitle();
           //au final la fonction map ne retiendra que le titre du role et non le reste du tbl
       })->toArray();
       $roles[] = 'ROLE_USER';
      
       return $roles;
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

        /**
         * @return Collection|Role[]
         */
        public function getUserRoles(): Collection
        {
            return $this->userRoles;
        }

        public function addUserRole(Role $userRole): self
        {
            if (!$this->userRoles->contains($userRole)) {
                $this->userRoles[] = $userRole;
                $userRole->addUser($this);
            }

            return $this;
        }

        public function removeUserRole(Role $userRole): self
        {
            if ($this->userRoles->contains($userRole)) {
                $this->userRoles->removeElement($userRole);
                $userRole->removeUser($this);
            }

            return $this;
        }

        public function getActive(): ?int
        {
            return $this->active;
        }

        public function setActive(int $active): self
        {
            $this->active = $active;

            return $this;
        }

        /**
         * @return Collection|SerialNumber[]
         */
        public function getUserSerialNumber(): Collection
        {
            return $this->userSerialNumber;
        }

        public function addUserSerialNumber(SerialNumber $userSerialNumber): self
        {
            if (!$this->userSerialNumber->contains($userSerialNumber)) {
                $this->userSerialNumber[] = $userSerialNumber;
                $userSerialNumber->addUserNumber($this);
            }

            return $this;
        }

        public function removeUserSerialNumber(SerialNumber $userSerialNumber): self
        {
            if ($this->userSerialNumber->contains($userSerialNumber)) {
                $this->userSerialNumber->removeElement($userSerialNumber);
                $userSerialNumber->removeUserNumber($this);
            }

            return $this;
        }
}
