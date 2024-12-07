<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Laminas\Config\Doctrine\Unit\DBAL\Tools\Console\Command\Database;

use Chubbyphp\Laminas\Config\Doctrine\DBAL\Tools\Console\Command\Database\CreateCommand;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Doctrine\DBAL\Tools\Console\ConnectionProvider;
use PHPUnit\Framework\TestCase;
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

    public function testExecuteFakeSqlite(): void
    {
        $dbName = \sprintf('sample-%s', uniqid());

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

        /** @var AbstractSchemaManager|MockObject $schemaManager */
        $schemaManager = $this->getMockByCalls(AbstractSchemaManager::class, [
            Call::create('createDatabase')->with($path),
        ]);

        /** @var Connection|MockObject $tmpConnection */
        $tmpConnection = $this->getMockByCalls(Connection::class, [
            Call::create('createSchemaManager')->with()->willReturn($schemaManager),
        ]);

        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $command = new CreateCommand($connectionProvider, static fn (array $params) => $tmpConnection);

        self::assertSame(0, $command->run($input, $output));

        self::assertSame(str_replace('dbname', $path, 'Created database dbname.'.PHP_EOL), $output->fetch());
    }

    public function testExecuteFakeSqliteWithMissingPath(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Connection does not contain a \'path\' or \'dbname\' parameter.');

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

        $command = new CreateCommand($connectionProvider, static fn (array $params) => null);

        $command->run($input, $output);
    }

    public function testExecuteFakePgsql(): void
    {
        $dbName = \sprintf('sample-%s', uniqid());

        /** @var Connection|MockObject $connection */
        $connection = $this->getMockByCalls(Connection::class, [
            Call::create('getParams')->with()->willReturn([
                'driver' => 'pdo_pgsql',
                'primary' => [
                    'url' => 'pgsql://root:root@localhost:5432?charset=utf8',
                    'dbname' => $dbName,
                ],
            ]),
        ]);

        /** @var ConnectionProvider|MockObject $connectionProvider */
        $connectionProvider = $this->getMockByCalls(ConnectionProvider::class, [
            Call::create('getConnection')->with('name')->willReturn($connection),
        ]);

        /** @var AbstractPlatform|MockObject $databasePlatform */
        $databasePlatform = $this->getMockByCalls(AbstractPlatform::class, [
            Call::create('quoteSingleIdentifier')->with($dbName)->willReturn('"'.$dbName.'"'),
        ]);

        /** @var AbstractSchemaManager|MockObject $schemaManager */
        $schemaManager = $this->getMockByCalls(AbstractSchemaManager::class, [
            Call::create('createDatabase')->with('"'.$dbName.'"'),
        ]);

        /** @var Connection|MockObject $tmpConnection */
        $tmpConnection = $this->getMockByCalls(Connection::class, [
            Call::create('getDatabasePlatform')->with()->willReturn($databasePlatform),
            Call::create('createSchemaManager')->with()->willReturn($schemaManager),
        ]);

        $input = new ArrayInput([
            '--connection' => 'name',
        ]);
        $output = new BufferedOutput();

        $command = new CreateCommand($connectionProvider, static fn (array $params) => $tmpConnection);

        self::assertSame(0, $command->run($input, $output));

        self::assertSame(str_replace('dbname', $dbName, 'Created database "dbname".'.PHP_EOL), $output->fetch());
    }

    public function testExecuteFakePgsqlDbExists(): void
    {
        $dbName = \sprintf('sample-%s', uniqid());

        /** @var Connection|MockObject $connection */
        $connection = $this->getMockByCalls(Connection::class, [
            Call::create('getParams')->with()->willReturn([
                'driver' => 'pdo_pgsql',
                'primary' => [
                    'url' => 'pgsql://root:root@localhost:5432?charset=utf8',
                    'dbname' => $dbName,
                ],
            ]),
        ]);

        /** @var ConnectionProvider|MockObject $connectionProvider */
        $connectionProvider = $this->getMockByCalls(ConnectionProvider::class, [
            Call::create('getDefaultConnection')->with()->willReturn($connection),
        ]);

        /** @var AbstractPlatform|MockObject $databasePlatform */
        $databasePlatform = $this->getMockByCalls(AbstractPlatform::class, [
            Call::create('quoteSingleIdentifier')->with($dbName)->willReturn('"'.$dbName.'"'),
        ]);

        /** @var AbstractSchemaManager|MockObject $schemaManager */
        $schemaManager = $this->getMockByCalls(AbstractSchemaManager::class, [
            Call::create('createDatabase')->with('"'.$dbName.'"')
                ->willThrowException(new \Exception('An exception occurred while executing a query: SQLSTATE[42P04]: Duplicate database: 7 ERROR:  database "'.$dbName.'" already exists')),
        ]);

        /** @var Connection|MockObject $tmpConnection */
        $tmpConnection = $this->getMockByCalls(Connection::class, [
            Call::create('getDatabasePlatform')->with()->willReturn($databasePlatform),
            Call::create('createSchemaManager')->with()->willReturn($schemaManager),
        ]);

        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $command = new CreateCommand($connectionProvider, static fn (array $params) => $tmpConnection);

        self::assertSame(1, $command->run($input, $output));

        $message = <<<'EOT'
            Could not create database "dbname".
            An exception occurred while executing a query: SQLSTATE[42P04]: Duplicate database: 7 ERROR:  database "dbname" already exists

            EOT;

        self::assertStringStartsWith(str_replace('dbname', $dbName, $message), $output->fetch());
    }

    public function testExecuteFakePgsqlDbExistsAndIfNotExistsTrue(): void
    {
        $dbName = \sprintf('sample-%s', uniqid());

        /** @var Connection|MockObject $connection */
        $connection = $this->getMockByCalls(Connection::class, [
            Call::create('getParams')->with()->willReturn([
                'driver' => 'pdo_pgsql',
                'primary' => [
                    'url' => 'pgsql://root:root@localhost:5432?charset=utf8',
                    'dbname' => $dbName,
                ],
            ]),
        ]);

        /** @var ConnectionProvider|MockObject $connectionProvider */
        $connectionProvider = $this->getMockByCalls(ConnectionProvider::class, [
            Call::create('getDefaultConnection')->with()->willReturn($connection),
        ]);

        /** @var AbstractPlatform|MockObject $databasePlatform */
        $databasePlatform = $this->getMockByCalls(AbstractPlatform::class, [
            Call::create('quoteSingleIdentifier')->with($dbName)->willReturn('"'.$dbName.'"'),
        ]);

        /** @var AbstractSchemaManager|MockObject $schemaManager */
        $schemaManager = $this->getMockByCalls(AbstractSchemaManager::class, [
            Call::create('listDatabases')->with()->willReturn([$dbName]),
        ]);

        /** @var Connection|MockObject $tmpConnection */
        $tmpConnection = $this->getMockByCalls(Connection::class, [
            Call::create('createSchemaManager')->with()->willReturn($schemaManager),
            Call::create('getDatabasePlatform')->with()->willReturn($databasePlatform),
        ]);

        $input = new ArrayInput([
            '--if-not-exists' => true,
        ]);

        $output = new BufferedOutput();

        $command = new CreateCommand($connectionProvider, static fn (array $params) => $tmpConnection);

        self::assertSame(0, $command->run($input, $output));

        self::assertSame(
            str_replace('dbname', $dbName, 'Database "dbname" already exists. Skipped.'.PHP_EOL),
            $output->fetch()
        );
    }
}
