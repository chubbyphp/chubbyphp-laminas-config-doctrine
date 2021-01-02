<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Laminas\Config\Doctrine\Unit\ODM\MongoDB\Tools\Console\Command;

use Chubbyphp\Laminas\Config\Doctrine\ODM\MongoDB\Tools\Console\Command\DocumentManagerCommand;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use Doctrine\ODM\MongoDB\DocumentManager;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
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
    use MockByCallsTrait;

    public function testRunWithNoneDocumentManager(): void
    {
        $input = new ArrayInput([
            'argument' => 'argumentValue',
            '--option' => 'optionValue',
        ]);

        $output = new BufferedOutput();

        /** @var DocumentManager $documentManager */
        $documentManager = $this->getMockByCalls(DocumentManager::class);

        $command = new class($documentManager) extends Command {
            private DocumentManager $documentManager;

            public function __construct(DocumentManager $documentManager)
            {
                parent::__construct();

                $this->documentManager = $documentManager;
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

            protected function execute(InputInterface $input, OutputInterface $output): void
            {
                /** @var DocumentManagerHelper $documentManagerHelper */
                $documentManagerHelper = $this->getHelperSet()->get('dm');

                Assert::assertSame($this->documentManager, $documentManagerHelper->getDocumentManager());

                $output->writeln('it works!');
            }
        };

        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with(DocumentManager::class)->willReturn($documentManager),
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

        /** @var DocumentManager $documentManager */
        $documentManager = $this->getMockByCalls(DocumentManager::class);

        $command = new class($documentManager) extends Command {
            private DocumentManager $documentManager;

            public function __construct(DocumentManager $documentManager)
            {
                parent::__construct();

                $this->documentManager = $documentManager;
            }

            protected function configure(): void
            {
                $this
                    ->setName('command:name')
                ;
            }

            protected function execute(InputInterface $input, OutputInterface $output): void
            {
                /** @var DocumentManagerHelper $documentManagerHelper */
                $documentManagerHelper = $this->getHelperSet()->get('dm');

                Assert::assertSame($this->documentManager, $documentManagerHelper->getDocumentManager());
            }
        };

        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with(DocumentManager::class.'documentManagerName')->willReturn($documentManager),
        ]);

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

        $exception = new class() extends \Exception implements NotFoundExceptionInterface {
        };

        $command = new class() extends Command {
            protected function configure(): void
            {
                $this
                    ->setName('command:name')
                ;
            }

            protected function execute(InputInterface $input, OutputInterface $output): void
            {
            }
        };

        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with(DocumentManager::class.'unknownDocumentManagerName')->willThrowException($exception),
        ]);

        $documentManagerCommand = new DocumentManagerCommand($command, $container);
        $documentManagerCommand->run($input, $output);
    }
}
