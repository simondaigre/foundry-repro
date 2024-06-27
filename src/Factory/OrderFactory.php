<?php

namespace App\Factory;

use App\Entity\Order;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Order>
 */
final class OrderFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Order::class;
    }

    protected function defaults(): array|callable
    {
        return [
            'customer' => CustomerFactory::new()->withUser(),
        ];
    }
}
