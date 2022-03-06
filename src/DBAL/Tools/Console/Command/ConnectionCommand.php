<?php

declare(strict_types=1);

namespace Chubbyphp\Laminas\Config\Doctrine\DBAL\Tools\Console\Command;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class ConnectionCommand extends Command
{
    public function __construct(private Command $command, private ContainerInterface $container)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName($this->command->getName());
        $this->setAliases($this->command->getAliases());
        $this->setDescription($this->command->getDescription());
        $this->setHelp($this->command->getHelp());
        $this->setDefinition($this->command->getDefinition());

        // dbal > 2.11
        if (!$this->getDefinition()->hasOption('connection')) {
            $this->addOption('connection', null, InputOption::VALUE_OPTIONAL, 'The connection to use for this command');
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string $connectionName */
        $connectionName = $input->getOption('connection');

        try {
            $connection = $this->container->get(Connection::class.$connectionName);
        } catch (NotFoundExceptionInterface $serviceNotFoundException) {
            throw new \InvalidArgumentException(sprintf('Missing connection with name "%s"', $connectionName), $serviceNotFoundException->getCode(), $serviceNotFoundException);
        }

        $this->command->setHelperSet(new HelperSet([
            'db' => new ConnectionHelper($connection),
        ]));

        return (int) $this->command->execute($input, $output);
    }
}
