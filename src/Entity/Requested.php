<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RequestedRepository")
 */
class Requested
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $requestedMotherResponse;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $requestedAt;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $requestedToken;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\SerialNumber", inversedBy="requesteds")
     * @ORM\JoinColumn(nullable=false)
     */
    private $requestedFor;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="requestedUsers")
     * @ORM\JoinColumn(nullable=false)
     */
    private $requestedBy;
    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $requestedAccepted;
     /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $requestedRefused;
    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $requestedBanned;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRequestedAt()
    {
        return $this->requestedAt;
    }

    public function setRequestedAt($requestedAt)
    {
        $this->requestedAt = $requestedAt;

        return $this;
    }

    public function getRequestedMotherResponse(): ?string
    {
        return $this->requestedMotherResponse;
    }

    public function setRequestedMotherResponse(string $requestedMotherResponse): self
    {
        $this->requestedMotherResponse = $requestedMotherResponse;

        return $this;
    }

    public function getRequestedToken()
    {
        return $this->requestedToken;
    }

    public function setRequestedToken($requestedToken)
    {
        $this->requestedToken = $requestedToken;

        return $this;
    }

    public function getRequestedFor(): ?SerialNumber
    {
        return $this->requestedFor;
    }

    public function setRequestedFor(?SerialNumber $requestedFor): self
    {
        $this->requestedFor = $requestedFor;

        return $this;
    }

    public function getRequestedBy(): ?User
    {
        return $this->requestedBy;
    }

    public function setRequestedBy(?User $requestedBy): self
    {
        $this->requestedBy = $requestedBy;

        return $this;
    }

    public function getRequestedAccepted(): ?bool
    {
        return $this->requestedAccepted;
    }

    public function setRequestedAccepted(?bool $requestedAccepted): self
    {
        $this->requestedAccepted = $requestedAccepted;

        return $this;
    }

    public function getRequestedRefused(): ?bool
    {
        return $this->requestedRefused;
    }

    public function setRequestedRefused(?bool $requestedRefused): self
    {
        $this->requestedRefused = $requestedRefused;

        return $this;
    }
    
    public function getRequestedBanned(): ?bool
    {
        return $this->requestedBanned;
    }

    public function setRequestedBanned(?bool $requestedBanned): self
    {
        $this->requestedBanned = $requestedBanned;

        return $this;
    }
   
}
