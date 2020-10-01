<?php

declare(strict_types=1);

namespace Chubbyphp\Laminas\Config\Doctrine\ODM\MongoDB\Tools\Console\Command;

use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Tools\Console\Helper\DocumentManagerHelper;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class DocumentManagerCommand extends Command
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

        $this->addOption('dm', null, InputOption::VALUE_OPTIONAL, 'The document manager to use for this command');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string $documentManagerName */
        $documentManagerName = $input->getOption('dm');

        try {
            $documentManager = $this->container->get(DocumentManager::class.$documentManagerName);
        } catch (NotFoundExceptionInterface $serviceNotFoundException) {
            throw new \InvalidArgumentException(sprintf('Missing document manager with name "%s"', $documentManagerName));
        }

        $this->command->setHelperSet(new HelperSet([
            'dm' => new DocumentManagerHelper($documentManager),
        ]));

        return (int) $this->command->execute($input, $output);
    }
}
