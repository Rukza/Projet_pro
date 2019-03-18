<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SerialNumberRepository")
 */
class SerialNumber
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $serialWristlet;

     /**
     * @ORM\Column(type="string", length=255)
     */
    private $wristletTitle;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $active;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $mailMother;

     /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="userSerialNumber")
     */
    private $userNumber;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Requested", mappedBy="requestedFor", orphanRemoval=true)
     */
    private $requesteds;



    public function __construct()
    {
        $this->userNumber = new ArrayCollection();
        $this->requesteds = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSerialWristlet(): ?string
    {
        return $this->serialWristlet;
    }

    public function setSerialWristlet(string $serialWristlet): self
    {
        $this->serialWristlet = $serialWristlet;

        return $this;
    }


    public function getWristletTitle(): ?string
    {
        return $this->wristletTitle;
    }

    public function setWristletTitle(string $wristletTitle): self
    {
        $this->wristletTitle = $wristletTitle;

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

    public function getMailMother(): ?string
    {
        return $this->mailMother;
    }

    public function setMailMother(string $mailMother): self
    {
        $this->mailMother = $mailMother;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getUserNumber(): Collection
    {
        return $this->userNumber;
    }

    public function addUserNumber(User $userNumber): self
    {
        if (!$this->userNumber->contains($userNumber)) {
            $this->userNumber[] = $userNumber;
        }

        return $this;
    }

    public function removeUserNumber(User $userNumber): self
    {
        if ($this->userNumber->contains($userNumber)) {
            $this->userNumber->removeElement($userNumber);
        }

        return $this;
    }

    /**
     * @return Collection|Requested[]
     */
    public function getRequesteds(): Collection
    {
        return $this->requesteds;
    }

    public function addRequested(Requested $requested): self
    {
        if (!$this->requesteds->contains($requested)) {
            $this->requesteds[] = $requested;
            $requested->setRequestedFor($this);
        }

        return $this;
    }

    public function removeRequested(Requested $requested): self
    {
        if ($this->requesteds->contains($requested)) {
            $this->requesteds->removeElement($requested);
            // set the owning side to null (unless already changed)
            if ($requested->getRequestedFor() === $this) {
                $requested->setRequestedFor(null);
            }
        }

        return $this;
    }
    public function __toString(){
        return $this->wristletTitle;
    }
}
