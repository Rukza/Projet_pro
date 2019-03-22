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
    private $requestedName;

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
    private $requestedRefused;

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

    public function getRequestedName(): ?string
    {
        return $this->requestedName;
    }

    public function setRequestedName(string $requestedName): self
    {
        $this->requestedName = $requestedName;

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
    
    public function getRequestedRefused(): ?bool
    {
        return $this->requestedRefused;
    }

    public function setRequestedRefused(?bool $requestedRefused): self
    {
        $this->requestedRefused = $requestedRefused;

        return $this;
    }
    public function __toString(){
        return $this->requestedName;
    }
}
