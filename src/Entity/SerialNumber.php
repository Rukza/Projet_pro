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
    private $Serial;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $Active;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="userSerialNumber")
     */
    private $UserNumber;

    public function __construct()
    {
        $this->UserNumber = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSerial(): ?string
    {
        return $this->Serial;
    }

    public function setSerial(string $Serial): self
    {
        $this->Serial = $Serial;

        return $this;
    }

    public function getActive(): ?bool
    {
        return $this->Active;
    }

    public function setActive(?bool $Active): self
    {
        $this->Active = $Active;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getUserNumber(): Collection
    {
        return $this->UserNumber;
    }

    public function addUserNumber(User $userNumber): self
    {
        if (!$this->UserNumber->contains($userNumber)) {
            $this->UserNumber[] = $userNumber;
        }

        return $this;
    }

    public function removeUserNumber(User $userNumber): self
    {
        if ($this->UserNumber->contains($userNumber)) {
            $this->UserNumber->removeElement($userNumber);
        }

        return $this;
    }
}
