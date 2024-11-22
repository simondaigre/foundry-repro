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

use App\Repository\AgencyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;

/**
 * @author Simon Daigre <simon@daig.re>
 */
#[ORM\Entity(repositoryClass: AgencyRepository::class)]
class Agency
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @var Collection<int, AgencyImage>
     */
    #[ORM\OneToMany(mappedBy: 'agency', targetEntity: AgencyImage::class, orphanRemoval: true)]
    private Collection $agencyImages;

    /**
     * @var Collection<int, BackUser>
     */
    #[ORM\ManyToMany(targetEntity: BackUser::class, mappedBy: 'agencies', cascade: ['persist'])]
    #[ORM\OrderBy(value: ['lastName' => Criteria::ASC])]
    private Collection $backUsers;

    public function __construct()
    {
        $this->backUsers = new ArrayCollection();
        $this->agencyImages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, AgencyImage>
     */
    public function getAgencyImages(): Collection
    {
        return $this->agencyImages;
    }

    public function addAgencyImage(AgencyImage $agencyImage): self
    {
        if (!$this->agencyImages->contains($agencyImage)) {
            $this->agencyImages->add($agencyImage);
            $agencyImage->setAgency($this);
        }

        return $this;
    }

    public function removeAgencyImage(AgencyImage $agencyImage): self
    {
        $this->agencyImages->removeElement($agencyImage);

        return $this;
    }

    /**
     * @return Collection<int, BackUser>
     */
    public function getBackUsers(): Collection
    {
        return $this->backUsers;
    }

    public function addBackUser(BackUser $backUser): self
    {
        if (!$this->backUsers->contains($backUser)) {
            $this->backUsers->add($backUser);
        }

        return $this;
    }

    public function removeBackUser(BackUser $backUser): self
    {
        $this->backUsers->removeElement($backUser);

        return $this;
    }
}
