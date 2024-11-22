<?php

declare(strict_types=1);

/*
 * This file is part of the Weelodge application.
 *
 * (c) Weelodge <contact@weelodge.fr>
 *
 * Proprietary and confidential
 * Unauthorized copying of this file, via any medium is strictly prohibited
 */

namespace App\Entity;

use App\Repository\BackUserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Simon Daigre <simon@daig.re>
 */
#[ORM\Entity(repositoryClass: BackUserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'Il y a déjà un compte enregistré avec cet email.')]
#[UniqueEntity(fields: ['uuid'])]
class BackUser implements UserInterface, PasswordAuthenticatedUserInterface
{
    final public const string ROLE_OTHER = 'ROLE_OTHER';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $lastName = null;

    #[ORM\Column(length: 128, unique: true)]
    #[Assert\Email]
    #[Assert\NotBlank]
    private ?string $email = null;

    #[ORM\Column(length: 128)]
    private ?string $password = null;

    private ?string $plainPassword = null;

    #[ORM\Column(type: Types::JSON)]
    private array $roles = ['ROLE_OTHER'];

    #[ORM\ManyToOne(targetEntity: Agency::class)]
    private ?Agency $defaultAgency = null;

    /**
     * @var Collection<int, Agency>
     */
    #[ORM\ManyToMany(targetEntity: Agency::class, inversedBy: 'backUsers')]
    private Collection $agencies;

    public function __construct()
    {
        $this->agencies = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFullName(): string
    {
        return $this->id
            ? ucwords(mb_strtolower(sprintf('%s %s', $this->firstName, $this->lastName)), ' -')
            : '';
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
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

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    /**
     * Returns the roles or permissions granted to the user for security.
     */
    public function getRoles(): array
    {
        return array_unique($this->roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function hasRole(string $role): bool
    {
        return \in_array($role, $this->getRoles(), true);
    }

    /**
     * Removes sensitive data from the user.
     *
     * {@inheritdoc}
     */
    public function eraseCredentials(): void
    {
        // if you had a plainPassword property, you'd nullify it here
        $this->plainPassword = null;
    }

    public function getDefaultAgency(): ?Agency
    {
        return $this->defaultAgency;
    }

    public function setDefaultAgency(?Agency $defaultAgency): self
    {
        $this->defaultAgency = $defaultAgency;

        return $this;
    }

    /**
     * @return Collection<int, Agency>
     */
    public function getAgencies(): Collection
    {
        return $this->agencies;
    }

    public function addAgency(Agency $agency): self
    {
        if (!$this->agencies->contains($agency)) {
            $this->agencies->add($agency);
            $agency->addBackUser($this);
        }

        return $this;
    }

    public function removeAgency(Agency $agency): self
    {
        if ($this->agencies->removeElement($agency)) {
            $agency->removeBackUser($this);
        }

        return $this;
    }

    public function __serialize(): array
    {
        return [
            $this->id,
            $this->email,
            $this->password,
            $this->firstName,
            $this->lastName,
        ];
    }

    public function __unserialize(array $data): void
    {
        [$this->id, $this->email, $this->password, $this->firstName, $this->lastName] = $data;
    }
}
