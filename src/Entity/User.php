<?php

declare(strict_types=1);

/*
 * This file is part of the Warehouse wareMACHINE.
 *
 * (c) Warehouse <contact@w44.fr>
 *
 * Proprietary and confidential
 * Unauthorized copying of this file, via any medium is strictly prohibited
 */

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\ApiPlatform\Provider\UserGenreProvider;
use App\Entity\Traits\ActiveTrait;
use App\Entity\Traits\TimestampableTrait;
use App\Enum\UserLinkedAccountProviderEnum;
use App\Repository\UserRepository;
use App\Security\TwoFactor\Provider\Sms\Model\TwoFactorInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use libphonenumber\PhoneNumber;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @author Simon Daigre <simon@daig.re>
 */
#[ApiResource(
    shortName: 'Me',
    operations: [
        new GetCollection(
            uriTemplate: '/me/favorite_genres',
            security: "is_granted('ROLE_USER')",
            input: false,
            provider: UserGenreProvider::class,
        ),
    ],
)]
#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements PasswordAuthenticatedUserInterface, TwoFactorInterface, UserInterface
{
    use ActiveTrait;
    use TimestampableTrait;

    final public const string ROLE_SUPERADMIN = 'ROLE_SUPERADMIN';

    final public const string ROLE_ADMIN = 'ROLE_ADMIN';

    final public const string ROLE_SCAN = 'ROLE_SCAN';

    final public const string ROLE_REFUND = 'ROLE_REFUND';

    final public const string ROLE_ARCHIVE = 'ROLE_ARCHIVE';

    final public const string ROLE_ACCREDITATION = 'ROLE_ACCREDITATION';

    final public const string ROLE_GUESTLIST = 'ROLE_GUESTLIST';

    final public const string ROLE_EXPORT = 'ROLE_EXPORT';

    final public const string ROLE_USER = 'ROLE_USER';

    final public const array ROLES = [
        'Super Admin' => self::ROLE_SUPERADMIN,
        'Admin' => self::ROLE_ADMIN,
        "Contrôle d'accès" => self::ROLE_SCAN,
        'label.refunds' => self::ROLE_REFUND,
        'Archives events' => self::ROLE_ARCHIVE,
        'Accréditations' => self::ROLE_ACCREDITATION,
        'Guest-list' => self::ROLE_GUESTLIST,
        'Export' => self::ROLE_EXPORT,
        'label.user' => self::ROLE_USER,
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 128)]
    private string $password;

    private ?string $plainPassword = null;

    /**
     * @var string[]
     */
    #[ORM\Column(type: Types::JSON)]
    private array $roles = [self::ROLE_USER];

    #[ORM\OneToOne(inversedBy: 'user', targetEntity: Customer::class, cascade: ['persist'], fetch: 'EAGER')]
    #[ORM\JoinColumn(nullable: false)]
    private Customer $customer;

    #[ORM\Column(nullable: true)]
    private ?string $smsAuthCode = null;

    /**
     * @var Collection<int, Artist>
     */
    #[ORM\ManyToMany(targetEntity: Artist::class, inversedBy: 'users')]
    private Collection $artists;

    /**
     * @var Collection<int, Event>
     */
    #[ORM\ManyToMany(targetEntity: Event::class)]
    private Collection $events;

    /**
     * @var Collection<int, UserLinkedAccount>
     */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: UserLinkedAccount::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $userLinkedAccounts;

    /**
     * @var Collection<int, GuestList>
     */
    #[ORM\ManyToMany(targetEntity: GuestList::class, inversedBy: 'users')]
    private Collection $guestLists;

    public function __construct()
    {
        $this->events = new ArrayCollection();
        $this->artists = new ArrayCollection();
        $this->userLinkedAccounts = new ArrayCollection();
        $this->guestLists = new ArrayCollection();
    }

    public function getUserIdentifier(): string
    {
        return $this->customer->getEmail();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPassword(): string
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

    public function setPlainPassword(?string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    /**
     * Returns the roles or permissions granted to the user for security.
     *
     * @return string[]
     */
    public function getRoles(): array
    {
        return array_unique($this->roles);
    }

    /**
     * @param string[] $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Removes sensitive data from the user.
     *
     * {@inheritdoc}
     */
    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }

    public function getCustomer(): Customer
    {
        return $this->customer;
    }

    public function setCustomer(Customer $customer): self
    {
        $this->customer = $customer;

        return $this;
    }

    public function isSmsAuthEnabled(): bool
    {
        return $this->getCustomer()->getPhoneNumber() instanceof PhoneNumber;
    }

    public function getPhoneNumber(): PhoneNumber
    {
        return $this->getCustomer()->getPhoneNumber();
    }

    public function getSmsAuthCode(): string
    {
        return $this->smsAuthCode;
    }

    public function setSmsAuthCode(string $authCode): self
    {
        $this->smsAuthCode = $authCode;

        return $this;
    }

    /**
     * @return Collection<int, Artist>
     */
    public function getArtists(): Collection
    {
        return $this->artists;
    }

    public function addArtist(Artist $artist): self
    {
        if (!$this->artists->contains($artist)) {
            $this->artists->add($artist);
        }

        return $this;
    }

    public function removeArtist(Artist $artist): self
    {
        $this->artists->removeElement($artist);

        return $this;
    }

    /**
     * @return Collection<int, Event>
     */
    public function getEvents(): Collection
    {
        return $this->events;
    }

    public function addEvent(Event $event): self
    {
        if (!$this->events->contains($event)) {
            $this->events->add($event);
        }

        return $this;
    }

    public function removeEvent(Event $event): self
    {
        $this->events->removeElement($event);

        return $this;
    }

    /**
     * @return Collection<int, UserLinkedAccount>
     */
    public function getUserLinkedAccounts(): Collection
    {
        return $this->userLinkedAccounts;
    }

    public function addUserLinked(UserLinkedAccount $userLinked): self
    {
        if (!$this->userLinkedAccounts->contains($userLinked)) {
            $this->userLinkedAccounts->add($userLinked);
            $userLinked->setUser($this);
        }

        return $this;
    }

    public function removeUserLinked(UserLinkedAccount $userLinked): self
    {
        $this->userLinkedAccounts->removeElement($userLinked);

        return $this;
    }

    public function getUserLinkedAccountForProvider(UserLinkedAccountProviderEnum $userLinkedAccountProviderEnum): ?UserLinkedAccount
    {
        return $this->userLinkedAccounts->findFirst(static fn (int $key, UserLinkedAccount $userLinkedAccount): bool => $userLinkedAccount->getProvider() === $userLinkedAccountProviderEnum);
    }

    /**
     * @return Collection<int, GuestList>
     */
    public function getGuestLists(): Collection
    {
        return $this->guestLists;
    }

    public function addGuestList(GuestList $guestList): static
    {
        if (!$this->guestLists->contains($guestList)) {
            $this->guestLists->add($guestList);
        }

        return $this;
    }

    public function removeGuestList(GuestList $guestList): static
    {
        $this->guestLists->removeElement($guestList);

        return $this;
    }
}
