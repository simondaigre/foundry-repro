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

use App\Entity\Sale;
use App\Repository\SaleRepository;
use Zenstruck\Foundry\FactoryCollection;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;
use Zenstruck\Foundry\Persistence\Proxy;
use Zenstruck\Foundry\Persistence\ProxyRepositoryDecorator;

/**
 * @method        Sale|Proxy                                     create(array|callable $attributes = [])
 * @method static Sale|Proxy                                     createOne(array $attributes = [])
 * @method static Sale|Proxy                                     find(object|array|mixed $criteria)
 * @method static Sale|Proxy                                     findOrCreate(array $attributes)
 * @method static Sale|Proxy                                     first(string $sortedField = 'id')
 * @method static Sale|Proxy                                     last(string $sortedField = 'id')
 * @method static Sale|Proxy                                     random(array $attributes = [])
 * @method static Sale|Proxy                                     randomOrCreate(array $attributes = [])
 * @method static Sale[]|Proxy[]                                 all()
 * @method static Sale[]|Proxy[]                                 createMany(int $number, array|callable $attributes = [])
 * @method static Sale[]|Proxy[]                                 createSequence(iterable|callable $sequence)
 * @method static Sale[]|Proxy[]                                 findBy(array $attributes)
 * @method static Sale[]|Proxy[]                                 randomRange(int $min, int $max, array $attributes = [])
 * @method static Sale[]|Proxy[]                                 randomSet(int $number, array $attributes = [])
 * @method        FactoryCollection<Sale|Proxy>                  many(int $min, int|null $max = null)
 * @method        FactoryCollection<Sale|Proxy>                  sequence(iterable|callable $sequence)
 * @method static ProxyRepositoryDecorator<Sale, SaleRepository> repository()
 *
 * @phpstan-method Sale&Proxy<Sale> create(array|callable $attributes = [])
 * @phpstan-method static Sale&Proxy<Sale> createOne(array $attributes = [])
 * @phpstan-method static Sale&Proxy<Sale> find(object|array|mixed $criteria)
 * @phpstan-method static Sale&Proxy<Sale> findOrCreate(array $attributes)
 * @phpstan-method static Sale&Proxy<Sale> first(string $sortedField = 'id')
 * @phpstan-method static Sale&Proxy<Sale> last(string $sortedField = 'id')
 * @phpstan-method static Sale&Proxy<Sale> random(array $attributes = [])
 * @phpstan-method static Sale&Proxy<Sale> randomOrCreate(array $attributes = [])
 * @phpstan-method static list<Sale&Proxy<Sale>> all()
 * @phpstan-method static list<Sale&Proxy<Sale>> createMany(int $number, array|callable $attributes = [])
 * @phpstan-method static list<Sale&Proxy<Sale>> createSequence(iterable|callable $sequence)
 * @phpstan-method static list<Sale&Proxy<Sale>> findBy(array $attributes)
 * @phpstan-method static list<Sale&Proxy<Sale>> randomRange(int $min, int $max, array $attributes = [])
 * @phpstan-method static list<Sale&Proxy<Sale>> randomSet(int $number, array $attributes = [])
 * @phpstan-method FactoryCollection<Sale&Proxy<Sale>> many(int $min, int|null $max = null)
 * @phpstan-method FactoryCollection<Sale&Proxy<Sale>> sequence(iterable|callable $sequence)
 *
 * @extends PersistentProxyObjectFactory<Sale>
 */
final class SaleFactory extends PersistentProxyObjectFactory
{
    protected function defaults(): array
    {
        return [
            'finderAgentOne' => SaleAgentFactory::new(),
        ];
    }

    public static function class(): string
    {
        return Sale::class;
    }
}
