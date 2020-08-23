<?php

declare(strict_types=1);

namespace Chubbyphp\Laminas\Config\Doctrine\ORM\Tools\Console\Command;

use Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class EntityManagerCommand extends Command
{
    /**
     * @var Command
     */
    private $command;

    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(Command $command, ContainerInterface $container)
    {
        $this->command = $command;
        $this->container = $container;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName($this->command->getName());
        $this->setAliases($this->command->getAliases());
        $this->setDescription($this->command->getDescription());
        $this->setHelp($this->command->getHelp());
        $this->setDefinition($this->command->getDefinition());

        $this->addOption('em', null, InputOption::VALUE_OPTIONAL, 'The entity manager to use for this command');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string $entityManagerName */
        $entityManagerName = $input->getOption('em');

        try {
            $entityManager = $this->container->get(EntityManager::class.$entityManagerName);
        } catch (NotFoundExceptionInterface $serviceNotFoundException) {
            throw new \InvalidArgumentException(sprintf('Missing entity manager with name "%s"', $entityManagerName));
        }

        $this->command->setHelperSet(new HelperSet([
            'db' => new ConnectionHelper($entityManager->getConnection()),
            'em' => new EntityManagerHelper($entityManager),
        ]));

        return (int) $this->command->execute($input, $output);
    }
}
