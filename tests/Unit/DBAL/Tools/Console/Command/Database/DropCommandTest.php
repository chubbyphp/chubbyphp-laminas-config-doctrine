<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Laminas\Config\Doctrine\Unit\DBAL\Tools\Console\Command\Database;

use Chubbyphp\Laminas\Config\Doctrine\DBAL\Tools\Console\Command\Database\DropCommand;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Tools\Console\ConnectionProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * @covers \Chubbyphp\Laminas\Config\Doctrine\DBAL\Tools\Console\Command\Database\DropCommand
 *
 * @internal
 */
final class DropCommandTest extends TestCase
{
    use MockByCallsTrait;

    public function testExecuteSqliteWithoutName(): void
    {
        $dbName = sprintf('sample-%s', uniqid());

        $path = sys_get_temp_dir().'/'.$dbName.'.db';

        $setupConnection = DriverManager::getConnection([
            'driver' => 'pdo_sqlite',
            'path' => $path,
        ]);

        $setupConnection->createSchemaManager()->createDatabase($path);

        /** @var Connection|MockObject $connection */
        $connection = $this->getMockByCalls(Connection::class, [
            Call::create('getParams')->with()->willReturn([
                'driver' => 'pdo_sqlite',
                'path' => $path,
            ]),
            Call::create('close'),
        ]);

        /** @var ConnectionProvider|MockObject $connectionProvider */
        $connectionProvider = $this->getMockByCalls(ConnectionProvider::class, [
            Call::create('getDefaultConnection')->with()->willReturn($connection),
        ]);

        $input = new ArrayInput([
            '--force' => true,
        ]);

        $output = new BufferedOutput();

        $command = new DropCommand($connectionProvider);

        self::assertSame(0, $command->run($input, $output));

        self::assertSame(str_replace('dbname', $path, 'Dropped database dbname.'.PHP_EOL), $output->fetch());
    }

    public function testExecuteSqliteWithoutNameAndMissingForce(): void
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

        /** @var ConnectionProvider|MockObject $connectionProvider */
        $connectionProvider = $this->getMockByCalls(ConnectionProvider::class, [
            Call::create('getDefaultConnection')->with()->willReturn($connection),
        ]);

        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $command = new DropCommand($connectionProvider);

        self::assertSame(2, $command->run($input, $output));

        $message = <<<'EOT'
            ATTENTION: This operation should not be executed in a production environment.

            Would drop the database /tmp/sample.db.
            Please run the operation with --force to execute
            All data will be lost!

            EOT;

        self::assertSame(str_replace('sample', $dbName, $message), $output->fetch());
    }

    public function testExecuteSqliteWithMissingPath(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Connection does not contain a \'path\' or \'dbname\' parameter.'
        );

        /** @var Connection|MockObject $connection */
        $connection = $this->getMockByCalls(Connection::class, [
            Call::create('getParams')->with()->willReturn([
                'driver' => 'pdo_sqlite',
            ]),
        ]);

        /** @var ConnectionProvider|MockObject $connectionProvider */
        $connectionProvider = $this->getMockByCalls(ConnectionProvider::class, [
            Call::create('getDefaultConnection')->with()->willReturn($connection),
        ]);

        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $command = new DropCommand($connectionProvider);

        $command->run($input, $output);
    }

    public function testExecutePgsqlWithName(): void
    {
        $dbName = sprintf('sample-%s', uniqid());

        $setupConnection = DriverManager::getConnection([
            'driver' => 'pdo_pgsql',
            'url' => getenv('POSTGRES_URL')
                ? getenv('POSTGRES_URL') : 'pgsql://root:root@localhost:5432?charset=utf8',
        ]);

        $setupConnection->createSchemaManager()->createDatabase('"'.$dbName.'"');

        $connection = DriverManager::getConnection([
            'driver' => 'pdo_pgsql',
            'url' => getenv('POSTGRES_URL')
                ? getenv('POSTGRES_URL') : 'pgsql://root:root@localhost:5432?charset=utf8',
            'dbname' => $dbName,
        ]);

        /** @var ConnectionProvider|MockObject $connectionProvider */
        $connectionProvider = $this->getMockByCalls(ConnectionProvider::class, [
            Call::create('getConnection')->with('name')->willReturn($connection),
        ]);

        $input = new ArrayInput([
            '--force' => true,
            '--connection' => 'name',
        ]);

        $output = new BufferedOutput();

        $command = new DropCommand($connectionProvider);

        self::assertSame(0, $command->run($input, $output));

        self::assertSame(str_replace('dbname', $dbName, 'Dropped database "dbname".'.PHP_EOL), $output->fetch());
    }

    public function testExecutePgsqlWithNameAndMissingDatabaseIfExists(): void
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

        /** @var ConnectionProvider|MockObject $connectionProvider */
        $connectionProvider = $this->getMockByCalls(ConnectionProvider::class, [
            Call::create('getDefaultConnection')->with()->willReturn($connection),
        ]);

        $input = new ArrayInput([
            '--if-exists' => true,
            '--force' => true,
        ]);

        $output = new BufferedOutput();

        $command = new DropCommand($connectionProvider);

        self::assertSame(0, $command->run($input, $output));

        self::assertSame(
            str_replace('dbname', $dbName, 'Database "dbname" doesn\'t exist. Skipped.'.PHP_EOL),
            $output->fetch()
        );
    }

    public function testExecutePgsqlWithNameAndMissingDatabase(): void
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

        /** @var ConnectionProvider|MockObject $connectionProvider */
        $connectionProvider = $this->getMockByCalls(ConnectionProvider::class, [
            Call::create('getDefaultConnection')->with()->willReturn($connection),
        ]);

        $input = new ArrayInput([
            '--force' => true,
        ]);

        $output = new BufferedOutput();

        $command = new DropCommand($connectionProvider);

        self::assertSame(1, $command->run($input, $output));

        $message = <<<'EOT'
            Could not drop database "dbname".
            An exception occurred while executing a query: SQLSTATE[3D000]: Invalid catalog name: 7 ERROR:  database "dbname" does not exist

            EOT;

        self::assertStringStartsWith(str_replace('dbname', $dbName, $message), $output->fetch());
    }
}
