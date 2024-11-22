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

use App\Repository\AgencyImageRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @author Simon Daigre <simon@daig.re>
 */
#[ORM\Entity(repositoryClass: AgencyImageRepository::class)]
class AgencyImage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'agencyImages')]
    #[ORM\JoinColumn(nullable: false)]
    private Agency $agency;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAgency(): Agency
    {
        return $this->agency;
    }

    public function setAgency(Agency $agency): self
    {
        $this->agency = $agency;

        return $this;
    }
}
