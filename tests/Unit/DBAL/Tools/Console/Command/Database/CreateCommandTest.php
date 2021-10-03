<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Laminas\Config\Doctrine\Unit\DBAL\Tools\Console\Command\Database;

use Chubbyphp\Laminas\Config\Doctrine\DBAL\Tools\Console\Command\Database\CreateCommand;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * @covers \Chubbyphp\Laminas\Config\Doctrine\DBAL\Tools\Console\Command\Database\CreateCommand
 *
 * @internal
 */
final class CreateCommandTest extends TestCase
{
    use MockByCallsTrait;

    public function testExecuteSqlite(): void
    {
        $dbName = sprintf('sample-%s', uniqid());

        $path = sys_get_temp_dir().'/'.$dbName.'.db';

        /** @var Connection|MockObject $connection */
        $connection = $this->getMockByCalls(Connection::class, [
            Call::create('getParams')->with()->willReturn([
                'driver' => 'pdo_sqlite',
                'path' => $path,
            ]),
        ]);

        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $command = new CreateCommand();
        $command->setHelperSet(new HelperSet([
            'db' => new ConnectionHelper($connection),
        ]));

        self::assertSame(0, $command->run($input, $output));

        self::assertSame(str_replace('dbname', $path, 'Created database dbname.'.PHP_EOL), $output->fetch());
    }

    public function testExecuteSqliteWithMissingPath(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Connection does not contain a \'path\' or \'dbname\' parameter.');

        /** @var Connection|MockObject $connection */
        $connection = $this->getMockByCalls(Connection::class, [
            Call::create('getParams')->with()->willReturn([
                'driver' => 'pdo_sqlite',
            ]),
        ]);

        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $command = new CreateCommand();
        $command->setHelperSet(new HelperSet([
            'db' => new ConnectionHelper($connection),
        ]));

        $command->run($input, $output);
    }

    public function testExecutePgsql(): void
    {
        $dbName = sprintf('sample-%s', uniqid());

        $connection = DriverManager::getConnection([
            'driver' => 'pdo_pgsql',
            'primary' => [
                'url' => getenv('POSTGRES_URL')
                    ? getenv('POSTGRES_URL') : 'pgsql://root:root@localhost:5432?charset=utf8',
                'dbname' => $dbName,
            ],
        ]);

        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $command = new CreateCommand();
        $command->setHelperSet(new HelperSet([
            'db' => new ConnectionHelper($connection),
        ]));

        self::assertSame(0, $command->run($input, $output));

        self::assertSame(str_replace('dbname', $dbName, 'Created database "dbname".'.PHP_EOL), $output->fetch());
    }

    public function testExecutePgsqlDbExists(): void
    {
        $dbName = sprintf('sample-%s', uniqid());

        $connection = DriverManager::getConnection([
            'driver' => 'pdo_pgsql',
            'primary' => [
                'url' => getenv('POSTGRES_URL')
                    ? getenv('POSTGRES_URL') : 'pgsql://root:root@localhost:5432?charset=utf8',
                'dbname' => $dbName,
            ],
        ]);

        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $command = new CreateCommand();
        $command->setHelperSet(new HelperSet([
            'db' => new ConnectionHelper($connection),
        ]));

        self::assertSame(0, $command->run($input, new BufferedOutput()));
        self::assertSame(1, $command->run($input, $output));

        $message = <<<'EOT'
            Could not create database "dbname".
            An exception occurred while executing 'CREATE DATABASE "dbname"':

            EOT;

        self::assertStringStartsWith(str_replace('dbname', $dbName, $message), $output->fetch());
    }

    public function testExecutePgsqlDbExistsAndIfNotExistsTrue(): void
    {
        $dbName = sprintf('sample-%s', uniqid());

        $connection = DriverManager::getConnection([
            'driver' => 'pdo_pgsql',
            'primary' => [
                'url' => getenv('POSTGRES_URL')
                    ? getenv('POSTGRES_URL') : 'pgsql://root:root@localhost:5432?charset=utf8',
                'dbname' => $dbName,
            ],
        ]);

        $input = new ArrayInput([
            '--if-not-exists' => true,
        ]);

        $output = new BufferedOutput();

        $command = new CreateCommand();
        $command->setHelperSet(new HelperSet([
            'db' => new ConnectionHelper($connection),
        ]));

        self::assertSame(0, $command->run($input, new BufferedOutput()));
        self::assertSame(0, $command->run($input, $output));

        self::assertSame(
            str_replace('dbname', $dbName, 'Database "dbname" already exists. Skipped.'.PHP_EOL),
            $output->fetch()
        );
    }

    public function testExecutePgsqlDbExistsAndIfNotExistsTrueWithMasterInsteadOfPrimary(): void
    {
        $dbName = sprintf('sample-%s', uniqid());

        $connection = DriverManager::getConnection([
            'driver' => 'pdo_pgsql',
            'master' => [
                'url' => getenv('POSTGRES_URL')
                    ? getenv('POSTGRES_URL') : 'pgsql://root:root@localhost:5432?charset=utf8',
                'dbname' => $dbName,
            ],
        ]);

        $input = new ArrayInput([
            '--if-not-exists' => true,
        ]);

        $output = new BufferedOutput();

        $command = new CreateCommand();
        $command->setHelperSet(new HelperSet([
            'db' => new ConnectionHelper($connection),
        ]));

        self::assertSame(0, $command->run($input, new BufferedOutput()));
        self::assertSame(0, $command->run($input, $output));

        self::assertSame(
            str_replace('dbname', $dbName, 'Database "dbname" already exists. Skipped.'.PHP_EOL),
            $output->fetch()
        );
    }
}
