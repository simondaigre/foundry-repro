<?php

namespace App\Command;

use App\Factory\OrderFactory;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:test-foundry',
    description: 'Add a short description for your command',
)]
class TestFoundryCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        dd(OrderFactory::createOne()->getCustomer()->getUser());

        return Command::SUCCESS;
    }
}
