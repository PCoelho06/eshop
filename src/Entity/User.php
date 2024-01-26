<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'Cet email est déjà utilisé', groups: ['register'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\NotBlank(message: 'Vous devez renseigner un email', groups: ['register'])]
    #[Assert\Email(
        message: 'L\'email {{ value }} n\'est pas un email valide !',
    )]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    #[Assert\NotBlank(message: 'Vous devez entrer un mot de passe', groups: ['register'])]
    #[Assert\Length(
        min: 8,
        minMessage: 'Votre mot de passe doit faire un minimun de 8 caractères',
    )]
    #[Assert\Regex(
        pattern: '#(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z]).{8,}#',
        match: true,
        message: 'le mot de passe doit contenir au moins 1 chiffre, 1 lettre minuscule, 1 lettre majuscule et doit faire au 
        moins 8 caractères',
    )]
    private ?string $password = null;

    #[Assert\NotBlank(message: 'Vous devez confirmer votre mot de passe', groups: ['register'])]
    #[Assert\EqualTo(
    propertyPath: 'password',
    message: 'Les deux mots de passe ne sont pas identiques',
    )]
    private ?string $confirmPassword;

    private $oldPassword;

    #[Assert\NotBlank(message: 'Vous devez entrer un mot de passe')]
    #[Assert\Length(
    min: 8,
    minMessage: 'Votre mot de passe doit faire un minimun de 8 caractères'
    )]
    private $newPassword;
    
    /*confirmation du password*/
    #[Assert\NotBlank(message: 'Vous devez confirmer votre nouveau mot de passe')]
    #[Assert\EqualTo(
    propertyPath: 'newPassword',
    message: 'Les deux mots de passe ne sont pas identiques'
    )]
    private $confirmNewPassword;

    #[Assert\NotBlank(
        message: 'Vous devez renseigner votre prénom', 
        groups: ['register']
    )]
    #[ORM\Column(length: 255)]
    private ?string $firstName = null;

    #[Assert\NotBlank(
        message: 'Vous devez renseigner votre nom', 
        groups: ['register']
    )]
    #[ORM\Column(length: 255)]
    private ?string $lastName = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get the value of confirmPassword
     */ 
    public function getConfirmPassword()
    {
        return $this->confirmPassword;
    }

    /**
     * Set the value of confirmPassword
     *
     * @return  self
     */ 
    public function setConfirmPassword($confirmPassword)
    {
        $this->confirmPassword = $confirmPassword;

        return $this;
    }

    /**
     * Get the value of oldPassword
     */ 
    public function getOldPassword()
    {
        return $this->oldPassword;
    }

    /**
     * Set the value of oldPassword
     *
     * @return  self
     */ 
    public function setOldPassword($oldPassword)
    {
        $this->oldPassword = $oldPassword;

        return $this;
    }

    /**
     * Get the value of newPassword
     */ 
    public function getNewPassword()
    {
        return $this->newPassword;
    }

    /**
     * Set the value of newPassword
     *
     * @return  self
     */ 
    public function setNewPassword($newPassword)
    {
        $this->newPassword = $newPassword;

        return $this;
    }

    /**
     * Get the value of confirmNewPassword
     */ 
    public function getConfirmNewPassword()
    {
        return $this->confirmNewPassword;
    }

    /**
     * Set the value of confirmNewPassword
     *
     * @return  self
     */ 
    public function setConfirmNewPassword($confirmNewPassword)
    {
        $this->confirmNewPassword = $confirmNewPassword;

        return $this;
    }
}
