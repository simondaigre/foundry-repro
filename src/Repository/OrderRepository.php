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

namespace App\Repository;

use App\Entity\Customer;
use App\Entity\Order;
use App\Enum\OrderCheckoutStateEnum;
use App\Enum\OrderShippingStateEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\DBAL\Types\Types;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Order|null find($id, $lockMode = null, $lockVersion = null)
 * @method Order|null findOneBy(array $criteria, array $orderBy = null)
 * @method Order[]    findAll()
 * @method Order[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @author Simon Daigre <simon@daig.re>
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    public function findValidOrdersCount(): int
    {
        return (int) $this
            ->createQueryBuilder('o')
            ->select('COUNT(o)')
            ->where('o.checkoutState = :completed')
            ->setParameter('completed', OrderCheckoutStateEnum::COMPLETED)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findPendingOrdersCount(): int
    {
        return (int) $this
            ->createQueryBuilder('o')
            ->select('COUNT(DISTINCT o)')
            ->join('o.orderProducts', 'op')
            ->where('o.shippingState = :ready')
            ->setParameter('ready', OrderShippingStateEnum::READY)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findMostRecentCart(Customer $customer): ?Order
    {
        return $this
            ->createQueryBuilder('o')
            ->andWhere('o.customer = :customer')
            ->setParameter('customer', $customer)
            ->orderBy('o.id', Criteria::DESC)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findCartShouldBeExpired(): array
    {
        return $this
            ->createQueryBuilder('o')
            ->andWhere('o.checkoutState = :cart')
            ->andWhere('NOW() > o.expiresAt')
            ->setParameter('cart', OrderCheckoutStateEnum::CART)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return array<Order>
     */
    public function getExportData(\DateTimeImmutable $dateBegin, \DateTimeImmutable $dateEnd): array
    {
        return $this
            ->createQueryBuilder('o')
            ->addSelect(['orderProducts', 'address', 'payment'])
            ->leftJoin('o.orderProducts', 'orderProducts')
            ->leftJoin('o.address', 'address')
            ->leftJoin('o.payment', 'payment')
            ->where('o.checkoutState = :completedState')
            ->andWhere('o.checkoutCompletedAt >= :dateBegin')
            ->andWhere('o.checkoutCompletedAt <= :dateEnd')
            ->setParameter('completedState', OrderCheckoutStateEnum::COMPLETED)
            ->setParameter('dateBegin', $dateBegin, Types::DATETIME_IMMUTABLE)
            ->setParameter('dateEnd', $dateEnd, Types::DATETIME_IMMUTABLE)
            ->getQuery()
            ->getResult()
        ;
    }
}
