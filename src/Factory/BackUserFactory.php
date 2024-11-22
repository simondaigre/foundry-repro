<?php

/*
 * This file is part of the Weelodge application.
 *
 * (c) Weelodge <contact@weelodge.fr>
 *
 * Proprietary and confidential
 * Unauthorized copying of this file, via any medium is strictly prohibited
 */

namespace App\Factory;

use App\Entity\BackUser;
use App\Repository\BackUserRepository;
use Zenstruck\Foundry\FactoryCollection;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;
use Zenstruck\Foundry\Persistence\Proxy;
use Zenstruck\Foundry\Persistence\ProxyRepositoryDecorator;

/**
 * @method        BackUser|Proxy                                         create(array|callable $attributes = [])
 * @method static BackUser|Proxy                                         createOne(array $attributes = [])
 * @method static BackUser|Proxy                                         find(object|array|mixed $criteria)
 * @method static BackUser|Proxy                                         findOrCreate(array $attributes)
 * @method static BackUser|Proxy                                         first(string $sortedField = 'id')
 * @method static BackUser|Proxy                                         last(string $sortedField = 'id')
 * @method static BackUser|Proxy                                         random(array $attributes = [])
 * @method static BackUser|Proxy                                         randomOrCreate(array $attributes = [])
 * @method static BackUser[]|Proxy[]                                     all()
 * @method static BackUser[]|Proxy[]                                     createMany(int $number, array|callable $attributes = [])
 * @method static BackUser[]|Proxy[]                                     createSequence(iterable|callable $sequence)
 * @method static BackUser[]|Proxy[]                                     findBy(array $attributes)
 * @method static BackUser[]|Proxy[]                                     randomRange(int $min, int $max, array $attributes = [])
 * @method static BackUser[]|Proxy[]                                     randomSet(int $number, array $attributes = [])
 * @method        FactoryCollection<BackUser|Proxy>                      many(int $min, int|null $max = null)
 * @method        FactoryCollection<BackUser|Proxy>                      sequence(iterable|callable $sequence)
 * @method static ProxyRepositoryDecorator<BackUser, BackUserRepository> repository()
 *
 * @phpstan-method BackUser&Proxy<BackUser> create(array|callable $attributes = [])
 * @phpstan-method static BackUser&Proxy<BackUser> createOne(array $attributes = [])
 * @phpstan-method static BackUser&Proxy<BackUser> find(object|array|mixed $criteria)
 * @phpstan-method static BackUser&Proxy<BackUser> findOrCreate(array $attributes)
 * @phpstan-method static BackUser&Proxy<BackUser> first(string $sortedField = 'id')
 * @phpstan-method static BackUser&Proxy<BackUser> last(string $sortedField = 'id')
 * @phpstan-method static BackUser&Proxy<BackUser> random(array $attributes = [])
 * @phpstan-method static BackUser&Proxy<BackUser> randomOrCreate(array $attributes = [])
 * @phpstan-method static list<BackUser&Proxy<BackUser>> all()
 * @phpstan-method static list<BackUser&Proxy<BackUser>> createMany(int $number, array|callable $attributes = [])
 * @phpstan-method static list<BackUser&Proxy<BackUser>> createSequence(iterable|callable $sequence)
 * @phpstan-method static list<BackUser&Proxy<BackUser>> findBy(array $attributes)
 * @phpstan-method static list<BackUser&Proxy<BackUser>> randomRange(int $min, int $max, array $attributes = [])
 * @phpstan-method static list<BackUser&Proxy<BackUser>> randomSet(int $number, array $attributes = [])
 * @phpstan-method FactoryCollection<BackUser&Proxy<BackUser>> many(int $min, int|null $max = null)
 * @phpstan-method FactoryCollection<BackUser&Proxy<BackUser>> sequence(iterable|callable $sequence)
 *
 * @extends PersistentProxyObjectFactory<BackUser>
 */
final class BackUserFactory extends PersistentProxyObjectFactory
{
    protected function defaults(): array
    {
        return [
            'firstName' => self::faker()->firstName(),
            'lastName' => self::faker()->lastName(),
            'email' => self::faker()->unique()->safeEmail(),
            'password' => '$2y$13$oYufbqeW3fTeGNv1xXLY0updII.Ktm0ZsgOzqc4.H.I8g6/jORImm', // my-password,
            'roles' => [BackUser::ROLE_OTHER],
            'agencies' => AgencyFactory::new()->many(1, 5),
        ];
    }

    public static function class(): string
    {
        return BackUser::class;
    }
}
