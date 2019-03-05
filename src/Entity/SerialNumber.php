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
     * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="userSerialNumber")
     */
    private $userNumber;

    public function __construct()
    {
        $this->userNumber = new ArrayCollection();
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

    public function setActive(?bool $Active): self
    {
        $this->Active = $active;

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
}
