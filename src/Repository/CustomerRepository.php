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

use App\Entity\Campaign;
use App\Entity\Customer;
use App\Entity\Device;
use App\Entity\Event;
use App\Enum\CampaignCanalEnum;
use App\Enum\CampaignTypeEnum;
use App\Enum\TicketStateEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Customer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Customer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Customer[]    findAll()
 * @method Customer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @author Simon Daigre <simon@daig.re>
 */
class CustomerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Customer::class);
    }

    public function findOrCreate(string $email, string $firstName, string $lastName): Customer
    {
        $customer = $this
            ->findOneBy([
                'email' => $email,
            ])
        ;

        if (!$customer instanceof Customer) {
            $customer = (new Customer())
                ->setFirstName($firstName)
                ->setLastName($lastName)
                ->setEmail($email);

            $entityManager = $this->getEntityManager();
            $entityManager->persist($customer);
            $entityManager->flush();
        }

        return $customer;
    }

    /**
     * @return string[]
     */
    public function findBestCustomers(): array
    {
        return $this
            ->createQueryBuilder('customer')
            ->addSelect('SUM(ticketing.price) AS HIDDEN amount')
            ->leftJoin('customer.tickets', 'tickets')
            ->leftJoin('tickets.ticketing', 'ticketing')
            ->where('tickets.state = :validState')
            ->groupBy('customer.id')
            ->orderBy('amount', Criteria::DESC)
            ->setParameter('validState', TicketStateEnum::VALID)
            ->setMaxResults(200)
            ->getQuery()
            ->getResult();
    }

    public function findCustomersForCampaign(Campaign $campaign): array
    {
        if (CampaignCanalEnum::PUSH === $campaign->getCanal()) {
            $qb = $this->getEntityManager()
                ->createQueryBuilder()
                ->from(Device::class, 'device')
                ->leftJoin('device.customer', 'customer');
        } else {
            $qb = $this
                ->createQueryBuilder('customer');
        }

        if ($campaign->isWithCriterions()) {
            $qb
                ->leftJoin('customer.tickets', 'tickets')
                ->leftJoin('tickets.ticketing', 'ticketing')
                ->leftJoin('ticketing.event', 'event')
            ;

            if ($campaign->getEventCriterions()->count() > 0) {
                $qb
                    ->orWhere('event IN (:events)')
                    ->setParameter('events', $campaign->getEventCriterions())
                ;
            }

            if ($campaign->getArtistCriterions()->count() > 0) {
                $qb
                    ->leftJoin('event.artists', 'artists')
                    ->orWhere('artists IN (:artists)')
                    ->setParameter('artists', $campaign->getArtistCriterions())
                ;
            }

            if ($campaign->getConceptCriterions()->count() > 0) {
                $qb
                    ->leftJoin('event.concept', 'concept')
                    ->orWhere('concept IN (:concepts)')
                    ->setParameter('concepts', $campaign->getConceptCriterions())
                ;
            }

            if ($campaign->getGenreCriterions()->count() > 0) {
                $qb
                    ->leftJoin('event.genres', 'genres')
                    ->orWhere('genres IN (:genres)')
                    ->setParameter('genres', $campaign->getGenreCriterions())
                ;
            }

            if ($campaign->isCancelledTicketsExcluded()) {
                $qb
                    ->andWhere('tickets.state = :validState')
                    ->setParameter('validState', TicketStateEnum::VALID);
            }

            if ($campaign->isPrivilegeOnly()) {
                $qb
                    ->innerJoin('customer.privilegeCustomer', 'privilegeCustomer');
            }
        }

        switch ($campaign->getCanal()) {
            case CampaignCanalEnum::EMAIL:
                $qb->select('customer.email')
                    ->groupBy('customer.id');
                break;
            case CampaignCanalEnum::SMS:
                $qb->select('customer.email')
                    ->andWhere("customer.phoneNumber LIKE '+33%'")
                    ->andWhere('customer.phoneNumber IS NOT NULL')
                    ->groupBy('customer.id');
                break;
            case CampaignCanalEnum::PUSH:
                $qb->select('device.uuid, device.pushToken')
                    ->orderBy('device.updatedAt')
                    ->groupBy('device.uuid');
                break;
        }

        if (CampaignTypeEnum::MARKETING === $campaign->getType()) {
            switch ($campaign->getCanal()) {
                case CampaignCanalEnum::EMAIL:
                    $qb->andWhere('customer.emailCommunication = 1');
                    break;
                case CampaignCanalEnum::SMS:
                    $qb->andWhere('customer.smsCommunication = 1');
                    break;
                default:
                    break;
            }
        }

        return $qb
            ->getQuery()
            ->getArrayResult();
    }

    /**
     * @return array<int, string>
     */
    public function findAverageAgesForEvent(Event $event): array
    {
        return $this->createQueryBuilder('customer')
            ->select('TIMESTAMPDIFF(YEAR, customer.birthday, event.dateBegin) AS age')
            ->leftJoin('customer.tickets', 'tickets')
            ->leftJoin('tickets.ticketing', 'ticketings')
            ->leftJoin('ticketings.event', 'event')
            ->where('customer.birthday IS NOT NULL')
            ->andWhere('event = :event')
            ->andWhere('tickets.state = :ticketStateValid')
            ->groupBy('customer.id')
            ->setParameter('event', $event)
            ->setParameter('ticketStateValid', TicketStateEnum::VALID)
            ->getQuery()
            ->getArrayResult();
    }

    public function findEventCustomersGender(Event $event): array
    {
        return $this
            ->createQueryBuilder('customer')
            ->select('customer.gender')
            ->leftJoin('customer.tickets', 'tickets')
            ->leftJoin('tickets.ticketing', 'ticketings')
            ->leftJoin('ticketings.event', 'event')
            ->where('event = :event')
            ->groupBy('customer.id')
            ->setParameter('event', $event)
            ->getQuery()
            ->getArrayResult();
    }

    public function findCustomersIsFirstVisit(Event $event): array
    {
        $subQuery = $this
            ->createQueryBuilder('customer_sub')
            ->select('MIN(event_sub.dateBegin)')
            ->leftJoin('customer_sub.tickets', 'tickets_sub')
            ->leftJoin('tickets_sub.ticketing', 'ticketings_sub')
            ->leftJoin('ticketings_sub.event', 'event_sub')
            ->where('customer_sub = customer');

        return $this
            ->createQueryBuilder('customer')
            ->select(
                'CASE WHEN event.dateBegin = ('.$subQuery.')
                 THEN \'Oui\'
                 ELSE \'Non\'
                 END AS is_oldest_event'
            )
            ->leftJoin('customer.tickets', 'tickets')
            ->leftJoin('tickets.ticketing', 'ticketings')
            ->leftJoin('ticketings.event', 'event')
            ->where('event = :event')
            ->groupBy('customer.id')
            ->setParameter('event', $event)
            ->getQuery()
            ->getResult();
    }
}
