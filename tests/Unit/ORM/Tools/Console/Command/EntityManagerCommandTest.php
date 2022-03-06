<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Laminas\Config\Doctrine\Unit\ORM\Tools\Console\Command;

use Chubbyphp\Laminas\Config\Doctrine\ORM\Tools\Console\Command\EntityManagerCommand;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper;
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
 * @covers \Chubbyphp\Laminas\Config\Doctrine\ORM\Tools\Console\Command\EntityManagerCommand
 *
 * @internal
 */
final class EntityManagerCommandTest extends TestCase
{
    use MockByCallsTrait;

    public function testRunWithNoneEntityManager(): void
    {
        $input = new ArrayInput([
            'argument' => 'argumentValue',
            '--option' => 'optionValue',
        ]);

        $output = new BufferedOutput();

        /** @var Connection $connection */
        $connection = $this->getMockByCalls(Connection::class);

        /** @var EntityManager $entityManager */
        $entityManager = $this->getMockByCalls(EntityManager::class, [
            Call::create('getConnection')->with()->willReturn($connection),
        ]);

        $command = new class($connection, $entityManager) extends Command {
            public function __construct(private Connection $connection, private EntityManager $entityManager)
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

            protected function execute(InputInterface $input, OutputInterface $output): void
            {
                /** @var ConnectionHelper $connectionHelper */
                $connectionHelper = $this->getHelperSet()->get('db');

                Assert::assertSame($this->connection, $connectionHelper->getConnection());

                /** @var EntityManagerHelper $entityManagerHelper */
                $entityManagerHelper = $this->getHelperSet()->get('em');

                Assert::assertSame($this->entityManager, $entityManagerHelper->getEntityManager());

                $output->writeln('it works!');
            }
        };

        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with(EntityManager::class)->willReturn($entityManager),
        ]);

        $entityManagerCommand = new EntityManagerCommand($command, $container);

        self::assertSame($command->getName(), $entityManagerCommand->getName());
        self::assertSame($command->getAliases(), $entityManagerCommand->getAliases());
        self::assertSame($command->getDescription(), $entityManagerCommand->getDescription());
        self::assertSame($command->getHelp(), $entityManagerCommand->getHelp());
        self::assertSame($command->getDefinition(), $entityManagerCommand->getDefinition());

        self::assertSame(0, $entityManagerCommand->run($input, $output));

        self::assertSame("it works!\n", $output->fetch());
    }

    public function testRunWithKnownEntityManager(): void
    {
        $input = new ArrayInput([
            '--em' => 'entityManagerName',
        ]);

        $output = new BufferedOutput();

        /** @var Connection $connection */
        $connection = $this->getMockByCalls(Connection::class);

        /** @var EntityManager $entityManager */
        $entityManager = $this->getMockByCalls(EntityManager::class, [
            Call::create('getConnection')->with()->willReturn($connection),
        ]);

        $command = new class($connection, $entityManager) extends Command {
            public function __construct(private Connection $connection, private EntityManager $entityManager)
            {
                parent::__construct();
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

                /** @var EntityManagerHelper $entityManagerHelper */
                $entityManagerHelper = $this->getHelperSet()->get('em');

                Assert::assertSame($this->entityManager, $entityManagerHelper->getEntityManager());
            }
        };

        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with(EntityManager::class.'entityManagerName')->willReturn($entityManager),
        ]);

        $entityManagerCommand = new EntityManagerCommand($command, $container);
        $entityManagerCommand->run($input, $output);
    }

    public function testRunWithUnknownEntityManager(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing entity manager with name "unknownEntityManagerName"');

        $input = new ArrayInput([
            '--em' => 'unknownEntityManagerName',
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
            Call::create('get')->with(EntityManager::class.'unknownEntityManagerName')->willThrowException($exception),
        ]);

        $entityManagerCommand = new EntityManagerCommand($command, $container);
        $entityManagerCommand->run($input, $output);
    }
}
