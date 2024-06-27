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
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Put;
use App\ApiPlatform\Dto\CustomerInputDto;
use App\ApiPlatform\Dto\CustomerOutputDto;
use App\ApiPlatform\Processor\CustomerProcessor;
use App\ApiPlatform\Provider\CustomerProvider;
use App\Entity\Traits\TimestampableTrait;
use App\Enum\GenderEnum;
use App\Enum\OrderCheckoutStateEnum;
use App\Enum\PrivilegeDepositStateEnum;
use App\Repository\CustomerRepository;
use Brick\Money\Money;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use libphonenumber\PhoneNumber;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use Misd\PhoneNumberBundle\Validator\Constraints\PhoneNumber as AssertPhoneNumber;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Notifier\Recipient\Recipient;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Simon Daigre <simon@daig.re>
 */
#[ORM\Entity(repositoryClass: CustomerRepository::class)]
#[ORM\Index(columns: ['email'], name: 'email_idx')]
#[UniqueEntity(fields: ['email'], message: 'Il y a déjà un compte enregistré avec cette adresse email.')]
#[UniqueEntity(fields: ['phoneNumber'], message: 'Ce numéro est déjà associé à un compte.')]
#[ApiResource(
    operations: [
        new Get(),
        new Put(), // @deprecated
        new Patch(),
    ],
    input: CustomerInputDto::class,
    output: CustomerOutputDto::class,
    provider: CustomerProvider::class,
    processor: CustomerProcessor::class,
)]
class Customer
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank]
    #[ORM\Column(length: 64)]
    private string $firstName;

    #[Assert\NotBlank]
    #[ORM\Column(length: 64)]
    private string $lastName;

    #[Assert\GreaterThan('1900-01-01', message: 'La date de naissance est invalide.')]
    #[Assert\LessThanOrEqual('-15 years', message: 'Vous devez avoir plus de 15 ans.')]
    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $birthday = null;

    #[AssertPhoneNumber(type: AssertPhoneNumber::MOBILE)]
    #[ORM\Column(type: 'phone_number', nullable: true)]
    private ?PhoneNumber $phoneNumber = null;

    #[Assert\Email]
    #[ORM\Column(length: 128, unique: true)]
    private ?string $email = null;

    /**
     * @var Collection<int, Ticket>
     */
    #[ORM\OneToMany(mappedBy: 'customer', targetEntity: Ticket::class)]
    private Collection $tickets;

    /**
     * @var Collection<int, PrivilegeDeposit>
     */
    #[ORM\OneToMany(mappedBy: 'customer', targetEntity: PrivilegeDeposit::class)]
    private Collection $privilegeDeposits;

    /**
     * @var Collection<int, Order>
     */
    #[ORM\OneToMany(mappedBy: 'customer', targetEntity: Order::class)]
    #[ORM\OrderBy(value: ['id' => Criteria::DESC])]
    private Collection $orders;

    #[ORM\Column]
    private bool $emailCommunication = false;

    #[ORM\Column]
    private bool $smsCommunication = false;

    /**
     * @var Collection<int, Address>
     */
    #[ORM\OneToMany(mappedBy: 'customer', targetEntity: Address::class)]
    private Collection $addresses;

    #[ORM\OneToOne(mappedBy: 'customer', targetEntity: User::class)]
    private ?User $user = null;

    /** @var Collection<int, Device> */
    #[ORM\OneToMany(mappedBy: 'customer', targetEntity: Device::class)]
    private Collection $devices;

    /**
     * @var Collection<int, Review>
     */
    #[ORM\OneToMany(mappedBy: 'customer', targetEntity: Review::class)]
    private Collection $reviews;

    /**
     * @var Collection<int, AccreditationRequest>
     */
    #[ORM\OneToMany(mappedBy: 'customer', targetEntity: AccreditationRequest::class, orphanRemoval: true)]
    private Collection $accreditationRequests;

    #[ORM\Column]
    private bool $deleted = false;

    /**
     * @var Collection<int, Guest>
     */
    #[ORM\OneToMany(mappedBy: 'customer', targetEntity: Guest::class, orphanRemoval: true)]
    private Collection $guests;

    #[ORM\OneToOne(mappedBy: 'customer', cascade: ['persist', 'remove'])]
    private ?PrivilegeCustomer $privilegeCustomer = null;

    #[ORM\Column(nullable: true)]
    private ?GenderEnum $gender = null;

    public function __construct()
    {
        $this->tickets = new ArrayCollection();
        $this->privilegeDeposits = new ArrayCollection();
        $this->orders = new ArrayCollection();
        $this->addresses = new ArrayCollection();
        $this->devices = new ArrayCollection();
        $this->reviews = new ArrayCollection();
        $this->accreditationRequests = new ArrayCollection();
        $this->guests = new ArrayCollection();
    }

    public function getFullName(): string
    {
        return sprintf('%s %s', $this->firstName, $this->lastName);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getBirthday(): ?\DateTimeImmutable
    {
        return $this->birthday;
    }

    public function setBirthday(?\DateTimeImmutable $birthday): self
    {
        $this->birthday = $birthday;

        return $this;
    }

    public function getPhoneNumber(): ?PhoneNumber
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?PhoneNumber $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return Collection<int, Ticket>
     */
    public function getTickets(): Collection
    {
        return $this->tickets;
    }

    public function addTicket(Ticket $ticket): self
    {
        if (!$this->tickets->contains($ticket)) {
            $this->tickets[] = $ticket;
            $ticket->setCustomer($this);
        }

        return $this;
    }

    public function removeTicket(Ticket $ticket): self
    {
        if ($this->tickets->contains($ticket)) {
            $this->tickets->removeElement($ticket);
        }

        return $this;
    }

    /**
     * @return Collection<int, PrivilegeDeposit>
     */
    public function getPrivilegeDeposits(): Collection
    {
        return $this->privilegeDeposits;
    }

    public function addPrivilegeDeposit(PrivilegeDeposit $privilegeDeposit): self
    {
        if (!$this->privilegeDeposits->contains($privilegeDeposit)) {
            $this->privilegeDeposits[] = $privilegeDeposit;
            $privilegeDeposit->setCustomer($this);
        }

        return $this;
    }

    public function removePrivilegeDeposit(PrivilegeDeposit $privilegeDeposit): self
    {
        if ($this->privilegeDeposits->contains($privilegeDeposit)) {
            $this->privilegeDeposits->removeElement($privilegeDeposit);
        }

        return $this;
    }

    /**
     * @return Collection<int, Order>
     */
    public function getOrders(bool $validOnly = false): Collection
    {
        if ($validOnly) {
            return $this->orders->filter(static fn (Order $order): bool => OrderCheckoutStateEnum::COMPLETED === $order->getCheckoutState());
        }

        return $this->orders;
    }

    public function addOrder(Order $order): self
    {
        if (!$this->orders->contains($order)) {
            $this->orders[] = $order;
            $order->setCustomer($this);
        }

        return $this;
    }

    public function removeOrder(Order $order): self
    {
        if ($this->orders->contains($order)) {
            $this->orders->removeElement($order);
            // set the owning side to null (unless already changed)
            if ($order->getCustomer() === $this) {
                $order->setCustomer(null);
            }
        }

        return $this;
    }

    public function hasEmailCommunication(): bool
    {
        return $this->emailCommunication;
    }

    public function setEmailCommunication(bool $emailCommunication): self
    {
        $this->emailCommunication = $emailCommunication;

        return $this;
    }

    public function hasSmsCommunication(): bool
    {
        return $this->smsCommunication;
    }

    public function setSmsCommunication(bool $smsCommunication): self
    {
        $this->smsCommunication = $smsCommunication;

        return $this;
    }

    /**
     * @return Collection<int, Address>
     */
    public function getAddresses(): Collection
    {
        return $this->addresses;
    }

    public function addAddress(Address $address): self
    {
        if (!$this->addresses->contains($address)) {
            $this->addresses[] = $address;
            $address->setCustomer($this);
        }

        return $this;
    }

    public function removeAddress(Address $address): self
    {
        if ($this->addresses->contains($address)) {
            $this->addresses->removeElement($address);
        }

        return $this;
    }

    public function getTicketTurnover(): Money
    {
        $amount = Money::zero('EUR');

        foreach ($this->getTickets() as $ticket) {
            if ($ticket->isValid()) {
                $amount = $amount->plus($ticket->getTicketing()->getPrice());
            }
        }

        return $amount;
    }

    public function getProductTurnover(): Money
    {
        $amount = Money::zero('EUR');

        foreach ($this->getOrders(true) as $order) {
            foreach ($order->getOrderProducts() as $orderProduct) {
                $amount = $amount->plus($orderProduct->getTotal());
            }
        }

        return $amount;
    }

    public function getPrivilegeTurnover(): Money
    {
        $amount = Money::zero('EUR');

        foreach ($this->getPrivilegeDeposits() as $privilegeDeposit) {
            if (PrivilegeDepositStateEnum::PAID !== $privilegeDeposit->getState()) {
                continue;
            }

            $amount = $amount->plus($privilegeDeposit->getAmount());
        }

        return $amount;
    }

    public function getTurnover(): Money
    {
        return $this->getPrivilegeTurnover()
            ->plus($this->getProductTurnover())
            ->plus($this->getTicketTurnover());
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, Device>
     */
    public function getDevices(): Collection
    {
        return $this->devices;
    }

    public function addDevice(Device $device): self
    {
        if (!$this->devices->contains($device)) {
            $this->devices[] = $device;
            $device->setCustomer($this);
        }

        return $this;
    }

    public function removeDevice(Device $device): self
    {
        if ($this->devices->contains($device)) {
            $this->devices->removeElement($device);
        }

        return $this;
    }

    /**
     * @return Collection<int, Review>
     */
    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    public function addReview(Review $review): self
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews->add($review);
            $review->setCustomer($this);
        }

        return $this;
    }

    public function removeReview(Review $review): self
    {
        $this->reviews->removeElement($review);

        return $this;
    }

    /**
     * @return Collection<int, AccreditationRequest>
     */
    public function getAccreditationRequests(): Collection
    {
        return $this->accreditationRequests;
    }

    public function addAccreditationRequest(AccreditationRequest $accreditation): self
    {
        if (!$this->accreditationRequests->contains($accreditation)) {
            $this->accreditationRequests->add($accreditation);
            $accreditation->setCustomer($this);
        }

        return $this;
    }

    public function removeAccreditationRequest(AccreditationRequest $accreditation): self
    {
        $this->accreditationRequests->removeElement($accreditation);

        return $this;
    }

    public function isDeleted(): bool
    {
        return $this->deleted;
    }

    public function setDeleted(bool $deleted): static
    {
        $this->deleted = $deleted;

        return $this;
    }

    /**
     * @return Collection<int, Guest>
     */
    public function getGuests(): Collection
    {
        return $this->guests;
    }

    public function addGuest(Guest $guest): static
    {
        if (!$this->guests->contains($guest)) {
            $this->guests->add($guest);
            $guest->setCustomer($this);
        }

        return $this;
    }

    public function removeGuest(Guest $guest): static
    {
        // set the owning side to null (unless already changed)
        if ($this->guests->removeElement($guest) && $guest->getCustomer() === $this) {
            $guest->setCustomer(null);
        }

        return $this;
    }

    public function asRecipient(): Recipient
    {
        return new Recipient(
            $this->email,
            $this->phoneNumber instanceof PhoneNumber
                ? PhoneNumberUtil::getInstance()->format($this->phoneNumber, PhoneNumberFormat::E164)
                : ''
        );
    }

    public function getPrivilegeCustomer(): ?PrivilegeCustomer
    {
        return $this->privilegeCustomer;
    }

    public function setPrivilegeCustomer(PrivilegeCustomer $privilegeCustomer): static
    {
        // set the owning side of the relation if necessary
        if ($privilegeCustomer->getCustomer() !== $this) {
            $privilegeCustomer->setCustomer($this);
        }

        $this->privilegeCustomer = $privilegeCustomer;

        return $this;
    }

    public function getGender(): ?GenderEnum
    {
        return $this->gender;
    }

    public function setGender(?GenderEnum $gender): static
    {
        $this->gender = $gender;

        return $this;
    }
}
