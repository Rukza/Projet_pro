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
     * @ORM\Column(type="datetime")
     */
    private $requestedAt;

    /**
     * @ORM\Column(type="string", length=255)
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

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRequestedAt(): ?\DateTimeInterface
    {
        return $this->requestedAt;
    }

    public function setRequestedAt(\DateTimeInterface $requestedAt): self
    {
        $this->requestedAt = $requestedAt;

        return $this;
    }

    public function getRequestedToken(): ?string
    {
        return $this->requestedToken;
    }

    public function setRequestedToken(string $requestedToken): self
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
}
