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

use App\DBAL\Types\MoneyType;
use App\Entity\Traits\TimestampableTrait;
use App\Entity\Traits\UuidableInterface;
use App\Entity\Traits\UuidableTrait;
use App\Enum\OrderCheckoutStateEnum;
use App\Enum\OrderPaymentStateEnum;
use App\Enum\OrderShippingStateEnum;
use App\Repository\OrderRepository;
use Brick\Money\Money;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @author Simon Daigre <simon@daig.re>
 */
#[ORM\Table(name: '`order`')]
#[ORM\Index(columns: ['checkout_state'], name: 'checkout_state_idx')]
#[ORM\Index(columns: ['uuid'], name: 'uuid_idx')]
#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[UniqueEntity(fields: ['uuid'])]
class Order implements UuidableInterface
{
    use TimestampableTrait;
    use UuidableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 8, nullable: true)]
    private ?string $reference = null;

    /**
     * @var Collection<int, OrderProduct>
     */
    #[ORM\OneToMany(mappedBy: 'order', targetEntity: OrderProduct::class, cascade: ['persist', 'remove'])]
    private Collection $orderProducts;

    /**
     * @var Collection<int, OrderTicketing>
     */
    #[ORM\OneToMany(mappedBy: 'order', targetEntity: OrderTicketing::class, cascade: ['persist', 'remove'])]
    private Collection $orderTicketings;

    #[ORM\ManyToOne(targetEntity: Customer::class, inversedBy: 'orders')]
    private ?Customer $customer = null;

    #[ORM\OneToOne(inversedBy: 'order', targetEntity: Address::class)]
    #[ORM\JoinColumn]
    private ?Address $address = null;

    #[ORM\Column(type: MoneyType::MONEY)]
    private Money $itemsPriceTotal;

    #[ORM\Column(type: MoneyType::MONEY)]
    private Money $priceTotal;

    #[ORM\Column(type: MoneyType::MONEY)]
    private Money $taxTotal;

    #[ORM\Column(enumType: OrderCheckoutStateEnum::class)]
    private OrderCheckoutStateEnum $checkoutState = OrderCheckoutStateEnum::CART;

    #[ORM\Column(enumType: OrderPaymentStateEnum::class)]
    private OrderPaymentStateEnum $paymentState = OrderPaymentStateEnum::CART;

    #[ORM\Column(enumType: OrderShippingStateEnum::class)]
    private OrderShippingStateEnum $shippingState = OrderShippingStateEnum::CART;

    #[ORM\OneToOne(mappedBy: 'order', targetEntity: Shipment::class, cascade: ['persist', 'remove'])]
    private ?Shipment $shipment = null;

    #[ORM\OneToOne(mappedBy: 'order', targetEntity: Payment::class, cascade: ['persist', 'remove'])]
    private ?Payment $payment = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $checkoutCompletedAt = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $expiresAt = null;

    /**
     * @var Collection<int, Refund>
     */
    #[ORM\OneToMany(mappedBy: 'order', targetEntity: Refund::class, orphanRemoval: true)]
    private Collection $refunds;

    public function __construct()
    {
        $this->orderProducts = new ArrayCollection();
        $this->orderTicketings = new ArrayCollection();
        $this->refunds = new ArrayCollection();

        $this->itemsPriceTotal = Money::zero('EUR');
        $this->taxTotal = Money::zero('EUR');
        $this->priceTotal = Money::zero('EUR');
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, OrderProduct>
     */
    public function getOrderProducts(): Collection
    {
        return $this->orderProducts;
    }

    public function addOrderProduct(OrderProduct $orderProduct): self
    {
        if (!$this->orderProducts->contains($orderProduct)) {
            $this->orderProducts[] = $orderProduct;
            $orderProduct->setOrder($this);
        }

        return $this;
    }

    public function removeOrderProduct(OrderProduct $orderProduct): self
    {
        if ($this->orderProducts->contains($orderProduct)) {
            $this->orderProducts->removeElement($orderProduct);
        }

        return $this;
    }

    /**
     * @return Collection<int, OrderTicketing>
     */
    public function getOrderTicketings(): Collection
    {
        return $this->orderTicketings;
    }

    public function addOrderTicketing(OrderTicketing $orderTicketing): self
    {
        if (!$this->orderTicketings->contains($orderTicketing)) {
            $this->orderTicketings[] = $orderTicketing;
            $orderTicketing->setOrder($this);
        }

        return $this;
    }

    public function removeOrderTicketing(OrderTicketing $orderTicketing): self
    {
        if ($this->orderTicketings->contains($orderTicketing)) {
            $this->orderTicketings->removeElement($orderTicketing);
        }

        return $this;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): self
    {
        $this->customer = $customer;

        return $this;
    }

    public function getAddress(): ?Address
    {
        return $this->address;
    }

    public function setAddress(?Address $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getItemsPriceTotal(): Money
    {
        return $this->itemsPriceTotal;
    }

    public function setItemsPriceTotal(Money $itemsPriceTotal): self
    {
        $this->itemsPriceTotal = $itemsPriceTotal;

        return $this;
    }

    public function getPriceTotal(): Money
    {
        return $this->priceTotal;
    }

    public function setPriceTotal(Money $priceTotal): self
    {
        $this->priceTotal = $priceTotal;

        return $this;
    }

    public function getTaxTotal(): Money
    {
        return $this->taxTotal;
    }

    public function setTaxTotal(Money $taxTotal): self
    {
        $this->taxTotal = $taxTotal;

        return $this;
    }

    public function getCheckoutState(): OrderCheckoutStateEnum
    {
        return $this->checkoutState;
    }

    public function setCheckoutState(OrderCheckoutStateEnum $checkoutState): self
    {
        $this->checkoutState = $checkoutState;

        return $this;
    }

    public function getPaymentState(): OrderPaymentStateEnum
    {
        return $this->paymentState;
    }

    public function setPaymentState(OrderPaymentStateEnum $paymentState): self
    {
        $this->paymentState = $paymentState;

        return $this;
    }

    public function getShippingState(): OrderShippingStateEnum
    {
        return $this->shippingState;
    }

    public function setShippingState(OrderShippingStateEnum $shippingState): self
    {
        $this->shippingState = $shippingState;

        return $this;
    }

    public function getCheckoutCompletedAt(): ?\DateTimeImmutable
    {
        return $this->checkoutCompletedAt;
    }

    public function setCheckoutCompletedAt(?\DateTimeImmutable $checkoutCompletedAt): self
    {
        $this->checkoutCompletedAt = $checkoutCompletedAt;

        return $this;
    }

    public function getShipment(): ?Shipment
    {
        return $this->shipment;
    }

    public function setShipment(?Shipment $shipment): self
    {
        $this->shipment = $shipment;

        return $this;
    }

    public function getPayment(): ?Payment
    {
        return $this->payment;
    }

    public function setPayment(?Payment $payment): self
    {
        $this->payment = $payment;

        return $this;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(?string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    public function getExpiresAt(): ?\DateTimeImmutable
    {
        return $this->expiresAt;
    }

    public function setExpiresAt(?\DateTimeImmutable $expiresAt): self
    {
        $this->expiresAt = $expiresAt;

        return $this;
    }

    /**
     * @return Collection<int, Refund>
     */
    public function getRefunds(): Collection
    {
        return $this->refunds;
    }

    public function addRefund(Refund $refund): self
    {
        if (!$this->refunds->contains($refund)) {
            $this->refunds[] = $refund;
            $refund->setOrder($this);
        }

        return $this;
    }

    public function removeRefund(Refund $refund): self
    {
        if ($this->refunds->contains($refund)) {
            $this->refunds->removeElement($refund);
        }

        return $this;
    }
}
