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

use App\Entity\SaleAgent;
use App\Repository\SaleAgentRepository;
use Zenstruck\Foundry\FactoryCollection;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;
use Zenstruck\Foundry\Persistence\Proxy;
use Zenstruck\Foundry\Persistence\ProxyRepositoryDecorator;

/**
 * @method        SaleAgent|Proxy                                          create(array|callable $attributes = [])
 * @method static SaleAgent|Proxy                                          createOne(array $attributes = [])
 * @method static SaleAgent|Proxy                                          find(object|array|mixed $criteria)
 * @method static SaleAgent|Proxy                                          findOrCreate(array $attributes)
 * @method static SaleAgent|Proxy                                          first(string $sortedField = 'id')
 * @method static SaleAgent|Proxy                                          last(string $sortedField = 'id')
 * @method static SaleAgent|Proxy                                          random(array $attributes = [])
 * @method static SaleAgent|Proxy                                          randomOrCreate(array $attributes = [])
 * @method static SaleAgent[]|Proxy[]                                      all()
 * @method static SaleAgent[]|Proxy[]                                      createMany(int $number, array|callable $attributes = [])
 * @method static SaleAgent[]|Proxy[]                                      createSequence(iterable|callable $sequence)
 * @method static SaleAgent[]|Proxy[]                                      findBy(array $attributes)
 * @method static SaleAgent[]|Proxy[]                                      randomRange(int $min, int $max, array $attributes = [])
 * @method static SaleAgent[]|Proxy[]                                      randomSet(int $number, array $attributes = [])
 * @method        FactoryCollection<SaleAgent|Proxy>                       many(int $min, int|null $max = null)
 * @method        FactoryCollection<SaleAgent|Proxy>                       sequence(iterable|callable $sequence)
 * @method static ProxyRepositoryDecorator<SaleAgent, SaleAgentRepository> repository()
 *
 * @phpstan-method SaleAgent&Proxy<SaleAgent> create(array|callable $attributes = [])
 * @phpstan-method static SaleAgent&Proxy<SaleAgent> createOne(array $attributes = [])
 * @phpstan-method static SaleAgent&Proxy<SaleAgent> find(object|array|mixed $criteria)
 * @phpstan-method static SaleAgent&Proxy<SaleAgent> findOrCreate(array $attributes)
 * @phpstan-method static SaleAgent&Proxy<SaleAgent> first(string $sortedField = 'id')
 * @phpstan-method static SaleAgent&Proxy<SaleAgent> last(string $sortedField = 'id')
 * @phpstan-method static SaleAgent&Proxy<SaleAgent> random(array $attributes = [])
 * @phpstan-method static SaleAgent&Proxy<SaleAgent> randomOrCreate(array $attributes = [])
 * @phpstan-method static list<SaleAgent&Proxy<SaleAgent>> all()
 * @phpstan-method static list<SaleAgent&Proxy<SaleAgent>> createMany(int $number, array|callable $attributes = [])
 * @phpstan-method static list<SaleAgent&Proxy<SaleAgent>> createSequence(iterable|callable $sequence)
 * @phpstan-method static list<SaleAgent&Proxy<SaleAgent>> findBy(array $attributes)
 * @phpstan-method static list<SaleAgent&Proxy<SaleAgent>> randomRange(int $min, int $max, array $attributes = [])
 * @phpstan-method static list<SaleAgent&Proxy<SaleAgent>> randomSet(int $number, array $attributes = [])
 * @phpstan-method FactoryCollection<SaleAgent&Proxy<SaleAgent>> many(int $min, int|null $max = null)
 * @phpstan-method FactoryCollection<SaleAgent&Proxy<SaleAgent>> sequence(iterable|callable $sequence)
 *
 * @extends PersistentProxyObjectFactory<SaleAgent>
 */
final class SaleAgentFactory extends PersistentProxyObjectFactory
{
    protected function defaults(): array
    {
        return [
            'backUser' => BackUserFactory::new(),
        ];
    }

    protected function initialize(): static
    {
        return $this
            ->afterInstantiate(static function (SaleAgent $saleAgent): void {
                $saleAgent->setAgency(
                    $saleAgent->getBackUser()->getDefaultAgency()
                );
            })
        ;
    }

    public static function class(): string
    {
        return SaleAgent::class;
    }
}
