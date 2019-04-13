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
 * message="Adresse mail incompatible, veuillez en rentrer une autre"
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
     */
    private $adress;

    /**
     * @ORM\Column(type="string", length=10)
     */

    private $postalCode;
    /**
     * @ORM\Column(type="string", length=255)
     */

    private $city;
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
     * @Assert\EqualTo(propertyPath="pswd", message="Vos mots de passe ne sont pas identiques")
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
     * @ORM\ManyToMany(targetEntity="App\Entity\Role", mappedBy="users")
     */
    private $userRoles;

    /**
	 * @ORM\Column(type="boolean", nullable=true)
	 */
    private $active;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Requested", mappedBy="requestedBy", orphanRemoval=true)
     */
    private $requestedUsers;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\SerialNumber", mappedBy="Mother", orphanRemoval=true)
     */
    private $MotherFor;

    public function __construct()
    {
        $this->userRoles = new ArrayCollection();
        $this->active = false;
        $this->requestedUsers = new ArrayCollection();
        $this->MotherFor = new ArrayCollection();
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
    
    public function getFullAdress(){
        return "{$this->adress} {$this->postalCode} {$this->city}";
            }
    public function getAdress(): ?string
    {
        return $this->adress;
    }

    public function setAdress(string $adress): self
    {
        $this->adress = $adress;

        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(string $postalCode): self
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

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
   
    public function getRoles()//return roles list in string
    {
       $roles = $this->userRoles->map(function($role){
           
           return $role->getTitle();
          
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

        public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(?bool $active): self
    {
        $this->active = $active;

        return $this;
    }

        
        /**
         * @return Collection|Requested[]
         */
        public function getRequestedUsers(): Collection
        {
            return $this->requestedUsers;
        }

        public function addRequestedUser(Requested $requestedUser): self
        {
            if (!$this->requestedUsers->contains($requestedUser)) {
                $this->requestedUsers[] = $requestedUser;
                $requestedUser->setRequestedBy($this);
            }

            return $this;
        }

        public function removeRequestedUser(Requested $requestedUser): self
        {
            if ($this->requestedUsers->contains($requestedUser)) {
                $this->requestedUsers->removeElement($requestedUser);
                // set the owning side to null (unless already changed)
                if ($requestedUser->getRequestedBy() === $this) {
                    $requestedUser->setRequestedBy(null);
                }
            }

            return $this;
        }
        public function __toString(){
            return $this->firstName;
        }

        /**
         * @return Collection|SerialNumber[]
         */
        public function getMotherFor(): Collection
        {
            return $this->MotherFor;
        }

        public function addMotherFor(SerialNumber $motherFor): self
        {
            if (!$this->MotherFor->contains($motherFor)) {
                $this->MotherFor[] = $motherFor;
                $motherFor->setMother($this);
            }

            return $this;
        }

        public function removeMotherFor(SerialNumber $motherFor): self
        {
            if ($this->MotherFor->contains($motherFor)) {
                $this->MotherFor->removeElement($motherFor);
                // set the owning side to null (unless already changed)
                if ($motherFor->getMother() === $this) {
                    $motherFor->setMother(null);
                }
            }

            return $this;
        }
}
