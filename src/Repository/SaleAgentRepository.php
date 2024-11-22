<?php

/*
 * This file is part of the Weelodge application.
 *
 * (c) Weelodge <contact@weelodge.fr>
 *
 * Proprietary and confidential
 * Unauthorized copying of this file, via any medium is strictly prohibited
 */

namespace App\Repository;

use App\Entity\SaleAgent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @author Simon Daigre <simon@daig.re>
 *
 * @extends ServiceEntityRepository<SaleAgent>
 *
 * @method SaleAgent|null find($id, $lockMode = null, $lockVersion = null)
 * @method SaleAgent|null findOneBy(array $criteria, array $orderBy = null)
 * @method SaleAgent[]    findAll()
 * @method SaleAgent[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SaleAgentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SaleAgent::class);
    }
}
