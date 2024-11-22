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

use App\Entity\AgencyImage;
use App\Repository\AgencyImageRepository;
use Zenstruck\Foundry\FactoryCollection;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;
use Zenstruck\Foundry\Persistence\Proxy;
use Zenstruck\Foundry\Persistence\ProxyRepositoryDecorator;

/**
 * @method        AgencyImage|Proxy                                            create(array|callable $attributes = [])
 * @method static AgencyImage|Proxy                                            createOne(array $attributes = [])
 * @method static AgencyImage|Proxy                                            find(object|array|mixed $criteria)
 * @method static AgencyImage|Proxy                                            findOrCreate(array $attributes)
 * @method static AgencyImage|Proxy                                            first(string $sortedField = 'id')
 * @method static AgencyImage|Proxy                                            last(string $sortedField = 'id')
 * @method static AgencyImage|Proxy                                            random(array $attributes = [])
 * @method static AgencyImage|Proxy                                            randomOrCreate(array $attributes = [])
 * @method static AgencyImage[]|Proxy[]                                        all()
 * @method static AgencyImage[]|Proxy[]                                        createMany(int $number, array|callable $attributes = [])
 * @method static AgencyImage[]|Proxy[]                                        createSequence(iterable|callable $sequence)
 * @method static AgencyImage[]|Proxy[]                                        findBy(array $attributes)
 * @method static AgencyImage[]|Proxy[]                                        randomRange(int $min, int $max, array $attributes = [])
 * @method static AgencyImage[]|Proxy[]                                        randomSet(int $number, array $attributes = [])
 * @method        FactoryCollection<AgencyImage|Proxy>                         many(int $min, int|null $max = null)
 * @method        FactoryCollection<AgencyImage|Proxy>                         sequence(iterable|callable $sequence)
 * @method static ProxyRepositoryDecorator<AgencyImage, AgencyImageRepository> repository()
 *
 * @phpstan-method AgencyImage&Proxy<AgencyImage> create(array|callable $attributes = [])
 * @phpstan-method static AgencyImage&Proxy<AgencyImage> createOne(array $attributes = [])
 * @phpstan-method static AgencyImage&Proxy<AgencyImage> find(object|array|mixed $criteria)
 * @phpstan-method static AgencyImage&Proxy<AgencyImage> findOrCreate(array $attributes)
 * @phpstan-method static AgencyImage&Proxy<AgencyImage> first(string $sortedField = 'id')
 * @phpstan-method static AgencyImage&Proxy<AgencyImage> last(string $sortedField = 'id')
 * @phpstan-method static AgencyImage&Proxy<AgencyImage> random(array $attributes = [])
 * @phpstan-method static AgencyImage&Proxy<AgencyImage> randomOrCreate(array $attributes = [])
 * @phpstan-method static list<AgencyImage&Proxy<AgencyImage>> all()
 * @phpstan-method static list<AgencyImage&Proxy<AgencyImage>> createMany(int $number, array|callable $attributes = [])
 * @phpstan-method static list<AgencyImage&Proxy<AgencyImage>> createSequence(iterable|callable $sequence)
 * @phpstan-method static list<AgencyImage&Proxy<AgencyImage>> findBy(array $attributes)
 * @phpstan-method static list<AgencyImage&Proxy<AgencyImage>> randomRange(int $min, int $max, array $attributes = [])
 * @phpstan-method static list<AgencyImage&Proxy<AgencyImage>> randomSet(int $number, array $attributes = [])
 * @phpstan-method FactoryCollection<AgencyImage&Proxy<AgencyImage>> many(int $min, int|null $max = null)
 * @phpstan-method FactoryCollection<AgencyImage&Proxy<AgencyImage>> sequence(iterable|callable $sequence)
 *
 * @extends PersistentProxyObjectFactory<AgencyImage>
 */
final class AgencyImageFactory extends PersistentProxyObjectFactory
{
    protected function defaults(): array
    {
        return [
            'agency' => AgencyFactory::new(),
        ];
    }

    public static function class(): string
    {
        return AgencyImage::class;
    }
}
