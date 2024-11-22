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

use App\Entity\Agency;
use App\Repository\AgencyRepository;
use Zenstruck\Foundry\FactoryCollection;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;
use Zenstruck\Foundry\Persistence\Proxy;
use Zenstruck\Foundry\Persistence\ProxyRepositoryDecorator;

/**
 * @method        Agency|Proxy                                       create(array|callable $attributes = [])
 * @method static Agency|Proxy                                       createOne(array $attributes = [])
 * @method static Agency|Proxy                                       find(object|array|mixed $criteria)
 * @method static Agency|Proxy                                       findOrCreate(array $attributes)
 * @method static Agency|Proxy                                       first(string $sortedField = 'id')
 * @method static Agency|Proxy                                       last(string $sortedField = 'id')
 * @method static Agency|Proxy                                       random(array $attributes = [])
 * @method static Agency|Proxy                                       randomOrCreate(array $attributes = [])
 * @method static Agency[]|Proxy[]                                   all()
 * @method static Agency[]|Proxy[]                                   createMany(int $number, array|callable $attributes = [])
 * @method static Agency[]|Proxy[]                                   createSequence(iterable|callable $sequence)
 * @method static Agency[]|Proxy[]                                   findBy(array $attributes)
 * @method static Agency[]|Proxy[]                                   randomRange(int $min, int $max, array $attributes = [])
 * @method static Agency[]|Proxy[]                                   randomSet(int $number, array $attributes = [])
 * @method        FactoryCollection<Agency|Proxy>                    many(int $min, int|null $max = null)
 * @method        FactoryCollection<Agency|Proxy>                    sequence(iterable|callable $sequence)
 * @method static ProxyRepositoryDecorator<Agency, AgencyRepository> repository()
 *
 * @phpstan-method Agency&Proxy<Agency> create(array|callable $attributes = [])
 * @phpstan-method static Agency&Proxy<Agency> createOne(array $attributes = [])
 * @phpstan-method static Agency&Proxy<Agency> find(object|array|mixed $criteria)
 * @phpstan-method static Agency&Proxy<Agency> findOrCreate(array $attributes)
 * @phpstan-method static Agency&Proxy<Agency> first(string $sortedField = 'id')
 * @phpstan-method static Agency&Proxy<Agency> last(string $sortedField = 'id')
 * @phpstan-method static Agency&Proxy<Agency> random(array $attributes = [])
 * @phpstan-method static Agency&Proxy<Agency> randomOrCreate(array $attributes = [])
 * @phpstan-method static list<Agency&Proxy<Agency>> all()
 * @phpstan-method static list<Agency&Proxy<Agency>> createMany(int $number, array|callable $attributes = [])
 * @phpstan-method static list<Agency&Proxy<Agency>> createSequence(iterable|callable $sequence)
 * @phpstan-method static list<Agency&Proxy<Agency>> findBy(array $attributes)
 * @phpstan-method static list<Agency&Proxy<Agency>> randomRange(int $min, int $max, array $attributes = [])
 * @phpstan-method static list<Agency&Proxy<Agency>> randomSet(int $number, array $attributes = [])
 * @phpstan-method FactoryCollection<Agency&Proxy<Agency>> many(int $min, int|null $max = null)
 * @phpstan-method FactoryCollection<Agency&Proxy<Agency>> sequence(iterable|callable $sequence)
 *
 * @extends PersistentProxyObjectFactory<Agency>
 */
final class AgencyFactory extends PersistentProxyObjectFactory
{
    protected function defaults(): array
    {
        return [
            'name' => self::faker()->words(3, true),
            'agencyImages' => AgencyImageFactory::new()->many(self::faker()->randomDigitNotNull()),
        ];
    }

    public static function class(): string
    {
        return Agency::class;
    }
}
