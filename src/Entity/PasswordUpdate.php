<?php

namespace App\Entity;
use Symfony\Component\Validator\Constraints as Assert;


class PasswordUpdate
{
    
    private $oldPassword;
    /**
     *@Assert\Length(min=8,minMessage="Votre mot de passe doit faire au moins 8 caractères")
     *@Assert\Regex("/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]).{8,}$/", message="Votre mot de passe doit contenir au minimum une majuscule, une minuscule et un chiffre")
     */
     
    private $newPassword;

    /**
     *@Assert\EqualTo(propertyPath="newPassword", message="Vos mot de passe ne sont pas identiques")
     *@Assert\Length(min=8,minMessage="Votre mot de passe doit faire au moins 8 caractères")
     *@Assert\Regex("/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]).{8,}$/", message="Votre mot de passe doit contenir au minimum une majuscule, une minuscule et un chiffre")
     */
    
    private $confirmPassword;

    public function getOldPassword(): ?string
    {
        return $this->oldPassword;
    }

    public function setOldPassword(string $oldPassword): self
    {
        $this->oldPassword = $oldPassword;

        return $this;
    }

    public function getNewPassword(): ?string
    {
        return $this->newPassword;
    }

    public function setNewPassword(string $newPassword): self
    {
        $this->newPassword = $newPassword;

        return $this;
    }

    public function getConfirmPassword(): ?string
    {
        return $this->confirmPassword;
    }

    public function setConfirmPassword(string $confirmPassword): self
    {
        $this->confirmPassword = $confirmPassword;

        return $this;
    }
}
