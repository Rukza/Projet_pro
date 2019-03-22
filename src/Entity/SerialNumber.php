<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $wristletTitle;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $active;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Requested", mappedBy="requestedFor", orphanRemoval=true)
     */
    private $requesteds;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="MotherFor")
     * @ORM\JoinColumn(nullable=true)
     */
    private $Mother;

    /**
    * 
    * @Assert\NotBlank()
    * 
    */
    public $checkConsent;


    public function __construct()
    {
        
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

    public function getMother(): ?User
    {
        return $this->Mother;
    }

    public function setMother(?User $Mother): self
    {
        $this->Mother = $Mother;

        return $this;
    }
}
