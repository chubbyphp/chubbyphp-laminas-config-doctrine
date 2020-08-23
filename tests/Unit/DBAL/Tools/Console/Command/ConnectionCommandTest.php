<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Laminas\Config\Doctrine\Unit\DBAL\Tools\Console\Command;

use Chubbyphp\Laminas\Config\Doctrine\DBAL\Tools\Console\Command\ConnectionCommand;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper;
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
 * @covers \Chubbyphp\Laminas\Config\Doctrine\DBAL\Tools\Console\Command\ConnectionCommand
 *
 * @internal
 */
final class ConnectionCommandTest extends TestCase
{
    use MockByCallsTrait;

    public function testRunWithNoneConnection(): void
    {
        $input = new ArrayInput([
            'argument' => 'argumentValue',
            '--option' => 'optionValue',
        ]);

        $output = new BufferedOutput();

        /** @var Connection $connection */
        $connection = $this->getMockByCalls(Connection::class);

        $command = new class($connection) extends Command {
            /**
             *  @var Connection
             */
            private $connection;

            public function __construct(Connection $connection)
            {
                parent::__construct();

                $this->connection = $connection;
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
                /** @var ConnectionHelper $connectionHelper */
                $connectionHelper = $this->getHelperSet()->get('db');

                Assert::assertSame($this->connection, $connectionHelper->getConnection());

                $output->writeln('it works!');
            }
        };

        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with(Connection::class)->willReturn($connection),
        ]);

        $connectionCommand = new ConnectionCommand($command, $container);

        self::assertSame($command->getName(), $connectionCommand->getName());
        self::assertSame($command->getAliases(), $connectionCommand->getAliases());
        self::assertSame($command->getDescription(), $connectionCommand->getDescription());
        self::assertSame($command->getHelp(), $connectionCommand->getHelp());
        self::assertSame($command->getDefinition(), $connectionCommand->getDefinition());

        self::assertSame(0, $connectionCommand->run($input, $output));

        self::assertSame("it works!\n", $output->fetch());
    }

    public function testRunWithKnownConnection(): void
    {
        $input = new ArrayInput([
            '--connection' => 'connectionName',
        ]);

        $output = new BufferedOutput();

        /** @var Connection $connection */
        $connection = $this->getMockByCalls(Connection::class);

        $command = new class($connection) extends Command {
            /**
             *  @var Connection
             */
            private $connection;

            public function __construct(Connection $connection)
            {
                parent::__construct();

                $this->connection = $connection;
            }

            protected function configure(): void
            {
                $this
                    ->setName('command:name')
                ;
            }

            protected function execute(InputInterface $input, OutputInterface $output): void
            {
                /** @var ConnectionHelper $connectionHelper */
                $connectionHelper = $this->getHelperSet()->get('db');

                Assert::assertSame($this->connection, $connectionHelper->getConnection());
            }
        };

        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with(Connection::class.'connectionName')->willReturn($connection),
        ]);

        $connectionCommand = new ConnectionCommand($command, $container);
        $connectionCommand->run($input, $output);
    }

    public function testRunWithUnknownConnection(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing connection with name "unknownConnectionName"');

        $input = new ArrayInput([
            '--connection' => 'unknownConnectionName',
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
            Call::create('get')->with(Connection::class.'unknownConnectionName')->willThrowException($exception),
        ]);

        $connectionCommand = new ConnectionCommand($command, $container);
        $connectionCommand->run($input, $output);
    }
}
