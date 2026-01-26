<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Laminas\Config\Doctrine\Unit\ODM\MongoDB\Tools\Console\Command;

use Chubbyphp\Laminas\Config\Doctrine\ODM\MongoDB\Tools\Console\Command\DocumentManagerCommand;
use Chubbyphp\Mock\MockMethod\WithException;
use Chubbyphp\Mock\MockMethod\WithReturn;
use Chubbyphp\Mock\MockObjectBuilder;
use Doctrine\ODM\MongoDB\DocumentManager;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @covers \Chubbyphp\Laminas\Config\Doctrine\ODM\MongoDB\Tools\Console\Command\DocumentManagerCommand
 *
 * @internal
 */
final class DocumentManagerCommandTest extends TestCase
{
    public function testRunWithNoneDocumentManager(): void
    {
        $input = new ArrayInput([
            'argument' => 'argumentValue',
            '--option' => 'optionValue',
        ]);

        $output = new BufferedOutput();

        $builder = new MockObjectBuilder();

        /** @var DocumentManager $documentManager */
        $documentManager = $builder->create(DocumentManager::class, []);

        $command = new class($documentManager) extends Command {
            public function __construct(private DocumentManager $documentManager)
            {
                parent::__construct();
            }

            protected function configure(): void
            {
                $this
                    ->setName('command:name')
                    ->setAliases(['command:alias'])
                    ->setDescription('command:description')
                    ->setHelp('command:help')
                    ->addArgument('argument', InputArgument::REQUIRED, 'Argument description')
                    ->addOption('option', 'o1', InputOption::VALUE_REQUIRED, 'Option description')
                ;
            }

            protected function execute(InputInterface $input, OutputInterface $output): int
            {
                /** @var DocumentManagerHelper $documentManagerHelper */
                $documentManagerHelper = $this->getHelperSet()->get('documentManager');

                Assert::assertSame($this->documentManager, $documentManagerHelper->getDocumentManager());

                $output->writeln('it works!');

                return 0;
            }
        };

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn('get', [DocumentManager::class], $documentManager),
        ]);

        $documentManagerCommand = new DocumentManagerCommand($command, $container);

        self::assertSame($command->getName(), $documentManagerCommand->getName());
        self::assertSame($command->getAliases(), $documentManagerCommand->getAliases());
        self::assertSame($command->getDescription(), $documentManagerCommand->getDescription());
        self::assertSame($command->getHelp(), $documentManagerCommand->getHelp());
        self::assertSame($command->getDefinition(), $documentManagerCommand->getDefinition());

        self::assertSame(0, $documentManagerCommand->run($input, $output));

        self::assertSame("it works!\n", $output->fetch());
    }

    public function testRunWithKnownDocumentManager(): void
    {
        $input = new ArrayInput([
            '--dm' => 'documentManagerName',
        ]);

        $output = new BufferedOutput();

        $builder = new MockObjectBuilder();

        /** @var DocumentManager $documentManager */
        $documentManager = $builder->create(DocumentManager::class, []);

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn('get', [DocumentManager::class.'documentManagerName'], $documentManager),
        ]);

        $command = new class($documentManager) extends Command {
            public function __construct(private DocumentManager $documentManager)
            {
                parent::__construct();
            }

            protected function configure(): void
            {
                $this
                    ->setName('command:name')
                ;
            }

            protected function execute(InputInterface $input, OutputInterface $output): int
            {
                /** @var DocumentManagerHelper $documentManagerHelper */
                $documentManagerHelper = $this->getHelperSet()->get('documentManager');

                Assert::assertSame($this->documentManager, $documentManagerHelper->getDocumentManager());

                return 0;
            }
        };

        $documentManagerCommand = new DocumentManagerCommand($command, $container);
        $documentManagerCommand->run($input, $output);
    }

    public function testRunWithUnknownDocumentManager(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing document manager with name "unknownDocumentManagerName"');

        $input = new ArrayInput([
            '--dm' => 'unknownDocumentManagerName',
        ]);

        $output = new BufferedOutput();

        $builder = new MockObjectBuilder();

        $exception = new class extends \Exception implements NotFoundExceptionInterface {};

        $command = new class extends Command {
            protected function configure(): void
            {
                $this
                    ->setName('command:name')
                ;
            }

            protected function execute(InputInterface $input, OutputInterface $output): int
            {
                return 0;
            }
        };

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithException(
                'get',
                [DocumentManager::class.'unknownDocumentManagerName'],
                $exception,
            ),
        ]);

        $documentManagerCommand = new DocumentManagerCommand($command, $container);
        $documentManagerCommand->run($input, $output);
    }

    public function testRunWithApplication(): void
    {
        $input = new ArrayInput([]);

        $output = new BufferedOutput();

        $builder = new MockObjectBuilder();

        /** @var DocumentManager $documentManager */
        $documentManager = $builder->create(DocumentManager::class, []);

        $innerCommandApplication = null;

        $command = new class($documentManager, $innerCommandApplication) extends Command {
            /**
             * @param null|Application $innerCommandApplication
             */
            public function __construct(private DocumentManager $documentManager, private &$innerCommandApplication)
            {
                parent::__construct();
            }

            protected function configure(): void
            {
                $this
                    ->setName('command:name')
                ;
            }

            protected function execute(InputInterface $input, OutputInterface $output): int
            {
                $this->innerCommandApplication = $this->getApplication();

                return 0;
            }
        };

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn('get', [DocumentManager::class], $documentManager),
        ]);

        $application = new Application();

        $documentManagerCommand = new DocumentManagerCommand($command, $container);
        $application->addCommand($documentManagerCommand);

        $documentManagerCommand->run($input, $output);

        self::assertSame($application, $innerCommandApplication);
    }
}
