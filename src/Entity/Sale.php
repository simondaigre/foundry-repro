<?php

/*
 * This file is part of the Weelodge application.
 *
 * (c) Weelodge <contact@weelodge.fr>
 *
 * Proprietary and confidential
 * Unauthorized copying of this file, via any medium is strictly prohibited
 */

namespace App\Entity;

use App\Repository\SaleRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @author Simon Daigre <simon@daig.re>
 */
#[ORM\Entity(repositoryClass: SaleRepository::class)]
class Sale
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[ORM\JoinColumn(nullable: false)]
    private ?SaleAgent $sellerAgentOne = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[ORM\JoinColumn]
    private ?SaleAgent $sellerAgentTwo = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[ORM\JoinColumn(nullable: false)]
    private ?SaleAgent $finderAgentOne = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[ORM\JoinColumn]
    private ?SaleAgent $finderAgentTwo = null;

    public function __construct()
    {
        $this->sellerAgentOne = new SaleAgent();
        $this->sellerAgentTwo = new SaleAgent();
        $this->finderAgentOne = new SaleAgent();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSellerAgentOne(): ?SaleAgent
    {
        return $this->sellerAgentOne;
    }

    public function setSellerAgentOne(?SaleAgent $sellerAgentOne): self
    {
        $this->sellerAgentOne = $sellerAgentOne;

        return $this;
    }

    public function getSellerAgentTwo(): ?SaleAgent
    {
        return $this->sellerAgentTwo;
    }

    public function setSellerAgentTwo(?SaleAgent $sellerAgentTwo): self
    {
        $this->sellerAgentTwo = $sellerAgentTwo;

        return $this;
    }

    /**
     * @return SaleAgent[]
     */
    public function getSellerAgents(): array
    {
        return array_filter([$this->sellerAgentOne, $this->sellerAgentTwo]);
    }

    public function getFinderAgentOne(): ?SaleAgent
    {
        return $this->finderAgentOne;
    }

    public function setFinderAgentOne(?SaleAgent $finderAgentOne): self
    {
        $this->finderAgentOne = $finderAgentOne;

        return $this;
    }

    public function getFinderAgentTwo(): ?SaleAgent
    {
        return $this->finderAgentTwo;
    }

    public function setFinderAgentTwo(?SaleAgent $finderAgentTwo): self
    {
        $this->finderAgentTwo = $finderAgentTwo;

        return $this;
    }

    /**
     * @return SaleAgent[]
     */
    public function getFinderAgents(): array
    {
        return array_filter([$this->finderAgentOne, $this->finderAgentTwo]);
    }

    /**
     * @return SaleAgent[]
     */
    public function getAgents(): array
    {
        return [...$this->getSellerAgents(), ...$this->getFinderAgents()];
    }
}
