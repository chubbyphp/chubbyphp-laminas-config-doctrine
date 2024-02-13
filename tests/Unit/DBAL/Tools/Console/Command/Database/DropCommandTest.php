<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Laminas\Config\Doctrine\Unit\DBAL\Tools\Console\Command\Database;

use Chubbyphp\Laminas\Config\Doctrine\DBAL\Tools\Console\Command\Database\DropCommand;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
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

    public function testExecuteFakeSqliteWithoutName(): void
    {
        $dbName = sprintf('sample-%s', uniqid());

        $path = sys_get_temp_dir().'/'.$dbName.'.db';

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

        /** @var AbstractSchemaManager|MockObject $schemaManager */
        $schemaManager = $this->getMockByCalls(AbstractSchemaManager::class, [
            Call::create('dropDatabase')->with($path),
        ]);

        /** @var Connection|MockObject $tmpConnection */
        $tmpConnection = $this->getMockByCalls(Connection::class, [
            Call::create('createSchemaManager')->with()->willReturn($schemaManager),
        ]);

        $input = new ArrayInput([
            '--force' => true,
        ]);

        $output = new BufferedOutput();

        $command = new DropCommand($connectionProvider, static fn (array $params) => $tmpConnection);

        self::assertSame(0, $command->run($input, $output));

        self::assertSame(str_replace('dbname', $path, 'Dropped database dbname.'.PHP_EOL), $output->fetch());
    }

    public function testExecuteFakeSqliteWithoutNameAndMissingForce(): void
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

        /** @var Connection|MockObject $tmpConnection */
        $tmpConnection = $this->getMockByCalls(Connection::class);

        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $command = new DropCommand($connectionProvider, static fn (array $params) => $tmpConnection);

        self::assertSame(2, $command->run($input, $output));

        $message = <<<'EOT'
            ATTENTION: This operation should not be executed in a production environment.

            Would drop the database /tmp/sample.db.
            Please run the operation with --force to execute
            All data will be lost!

            EOT;

        self::assertSame(str_replace('sample', $dbName, $message), $output->fetch());
    }

    public function testExecuteFakeSqliteWithMissingPath(): void
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

        /** @var Connection|MockObject $tmpConnection */
        $tmpConnection = $this->getMockByCalls(Connection::class);

        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $command = new DropCommand($connectionProvider, static fn (array $params) => $tmpConnection);

        $command->run($input, $output);
    }

    public function testExecuteFakePgsqlWithName(): void
    {
        $dbName = sprintf('sample-%s', uniqid());

        /** @var Connection|MockObject $connection */
        $connection = $this->getMockByCalls(Connection::class, [
            Call::create('getParams')->with()->willReturn([
                'driver' => 'pdo_pgsql',
                'primary' => [
                    'url' => 'pgsql://root:root@localhost:5432?charset=utf8',
                    'dbname' => $dbName,
                ],
            ]),
            Call::create('close'),
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
            Call::create('dropDatabase')->with('"'.$dbName.'"'),
        ]);

        /** @var Connection|MockObject $tmpConnection */
        $tmpConnection = $this->getMockByCalls(Connection::class, [
            Call::create('getDatabasePlatform')->with()->willReturn($databasePlatform),
            Call::create('createSchemaManager')->with()->willReturn($schemaManager),
        ]);

        $input = new ArrayInput([
            '--force' => true,
            '--connection' => 'name',
        ]);

        $output = new BufferedOutput();

        $command = new DropCommand($connectionProvider, static fn (array $params) => $tmpConnection);

        self::assertSame(0, $command->run($input, $output));

        self::assertSame(str_replace('dbname', $dbName, 'Dropped database "dbname".'.PHP_EOL), $output->fetch());
    }

    public function testExecuteFakePgsqlWithNameAndMissingDatabaseIfExists(): void
    {
        $dbName = sprintf('sample-%s', uniqid());

        /** @var Connection|MockObject $connection */
        $connection = $this->getMockByCalls(Connection::class, [
            Call::create('getParams')->with()->willReturn([
                'driver' => 'pdo_pgsql',
                'primary' => [
                    'url' => 'pgsql://root:root@localhost:5432?charset=utf8',
                    'dbname' => $dbName,
                ],
            ]),
            Call::create('close'),
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
            Call::create('listDatabases')->with()->willReturn([]),
        ]);

        /** @var Connection|MockObject $tmpConnection */
        $tmpConnection = $this->getMockByCalls(Connection::class, [
            Call::create('createSchemaManager')->with()->willReturn($schemaManager),
            Call::create('getDatabasePlatform')->with()->willReturn($databasePlatform),
        ]);

        $input = new ArrayInput([
            '--if-exists' => true,
            '--force' => true,
        ]);

        $output = new BufferedOutput();

        $command = new DropCommand($connectionProvider, static fn (array $params) => $tmpConnection);

        self::assertSame(0, $command->run($input, $output));

        self::assertSame(
            str_replace('dbname', $dbName, 'Database "dbname" doesn\'t exist. Skipped.'.PHP_EOL),
            $output->fetch()
        );
    }

    public function testExecuteFakePgsqlWithNameAndMissingDatabase(): void
    {
        $dbName = sprintf('sample-%s', uniqid());

        /** @var Connection|MockObject $connection */
        $connection = $this->getMockByCalls(Connection::class, [
            Call::create('getParams')->with()->willReturn([
                'driver' => 'pdo_pgsql',
                'primary' => [
                    'url' => 'pgsql://root:root@localhost:5432?charset=utf8',
                    'dbname' => $dbName,
                ],
            ]),
            Call::create('close'),
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
            Call::create('dropDatabase')->with('"'.$dbName.'"')
                ->willThrowException(new \Exception('An exception occurred while executing a query: SQLSTATE[3D000]: Invalid catalog name: 7 ERROR:  database "'.$dbName.'" does not exist')),
        ]);

        /** @var Connection|MockObject $tmpConnection */
        $tmpConnection = $this->getMockByCalls(Connection::class, [
            Call::create('getDatabasePlatform')->with()->willReturn($databasePlatform),
            Call::create('createSchemaManager')->with()->willReturn($schemaManager),
        ]);

        $input = new ArrayInput([
            '--force' => true,
        ]);

        $output = new BufferedOutput();

        $command = new DropCommand($connectionProvider, static fn (array $params) => $tmpConnection);

        self::assertSame(1, $command->run($input, $output));

        $message = <<<'EOT'
            Could not drop database "dbname".
            An exception occurred while executing a query: SQLSTATE[3D000]: Invalid catalog name: 7 ERROR:  database "dbname" does not exist

            EOT;

        self::assertStringStartsWith(str_replace('dbname', $dbName, $message), $output->fetch());
    }
}
