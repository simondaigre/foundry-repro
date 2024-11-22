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

namespace App\Repository;

use App\Entity\BackUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @author Simon Daigre <simon@daig.re>
 *
 * @extends ServiceEntityRepository<BackUser>
 *
 * @method BackUser|null find($id, $lockMode = null, $lockVersion = null)
 * @method BackUser|null findOneBy(array $criteria, array $orderBy = null)
 * @method BackUser[]    findAll()
 * @method BackUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BackUserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BackUser::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof BackUser) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->flush();
    }
}
