<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Laminas\Config\Doctrine\Unit\DBAL\Tools\Console\Command\Database;

use Chubbyphp\Laminas\Config\Doctrine\DBAL\Tools\Console\Command\Database\DropCommand;
use Chubbyphp\Mock\MockMethod\WithException;
use Chubbyphp\Mock\MockMethod\WithoutReturn;
use Chubbyphp\Mock\MockMethod\WithReturn;
use Chubbyphp\Mock\MockObjectBuilder;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Doctrine\DBAL\Tools\Console\ConnectionProvider;
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
    public function testExecuteFakeSqliteWithoutName(): void
    {
        $dbName = \sprintf('sample-%s', uniqid());

        $path = sys_get_temp_dir().'/'.$dbName.'.db';

        $builder = new MockObjectBuilder();

        /** @var Connection $connection */
        $connection = $builder->create(Connection::class, [
            new WithReturn('getParams', [], [
                'driver' => 'pdo_sqlite',
                'path' => $path,
            ]),
            new WithoutReturn('close', []),
        ]);

        /** @var ConnectionProvider $connectionProvider */
        $connectionProvider = $builder->create(ConnectionProvider::class, [
            new WithReturn('getDefaultConnection', [], $connection),
        ]);

        /** @var AbstractSchemaManager $schemaManager */
        $schemaManager = $builder->create(AbstractSchemaManager::class, [
            new WithoutReturn('dropDatabase', [$path]),
        ]);

        /** @var Connection $tmpConnection */
        $tmpConnection = $builder->create(Connection::class, [
            new WithReturn('createSchemaManager', [], $schemaManager),
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
        $dbName = \sprintf('sample-%s', uniqid());

        $path = sys_get_temp_dir().'/'.$dbName.'.db';

        $builder = new MockObjectBuilder();

        /** @var Connection $connection */
        $connection = $builder->create(Connection::class, [
            new WithReturn('getParams', [], [
                'driver' => 'pdo_sqlite',
                'path' => $path,
            ]),
        ]);

        /** @var ConnectionProvider $connectionProvider */
        $connectionProvider = $builder->create(ConnectionProvider::class, [
            new WithReturn('getDefaultConnection', [], $connection),
        ]);

        /** @var Connection $tmpConnection */
        $tmpConnection = $builder->create(Connection::class, []);

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

        $builder = new MockObjectBuilder();

        /** @var Connection $connection */
        $connection = $builder->create(Connection::class, [
            new WithReturn('getParams', [], [
                'driver' => 'pdo_sqlite',
            ]),
        ]);

        /** @var ConnectionProvider $connectionProvider */
        $connectionProvider = $builder->create(ConnectionProvider::class, [
            new WithReturn('getDefaultConnection', [], $connection),
        ]);

        /** @var Connection $tmpConnection */
        $tmpConnection = $builder->create(Connection::class, []);

        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $command = new DropCommand($connectionProvider, static fn (array $params) => $tmpConnection);

        $command->run($input, $output);
    }

    public function testExecuteFakePgsqlWithName(): void
    {
        $dbName = \sprintf('sample-%s', uniqid());

        $builder = new MockObjectBuilder();

        /** @var Connection $connection */
        $connection = $builder->create(Connection::class, [
            new WithReturn('getParams', [], [
                'driver' => 'pdo_pgsql',
                'primary' => [
                    'url' => 'pgsql://root:root@localhost:5432?charset=utf8',
                    'dbname' => $dbName,
                ],
            ]),
            new WithoutReturn('close', []),
        ]);

        /** @var ConnectionProvider $connectionProvider */
        $connectionProvider = $builder->create(ConnectionProvider::class, [
            new WithReturn('getConnection', ['name'], $connection),
        ]);

        /** @var AbstractPlatform $databasePlatform */
        $databasePlatform = $builder->create(AbstractPlatform::class, [
            new WithReturn('quoteSingleIdentifier', [$dbName], '"'.$dbName.'"'),
        ]);

        /** @var AbstractSchemaManager $schemaManager */
        $schemaManager = $builder->create(AbstractSchemaManager::class, [
            new WithoutReturn('dropDatabase', ['"'.$dbName.'"']),
        ]);

        /** @var Connection $tmpConnection */
        $tmpConnection = $builder->create(Connection::class, [
            new WithReturn('getDatabasePlatform', [], $databasePlatform),
            new WithReturn('createSchemaManager', [], $schemaManager),
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
        $dbName = \sprintf('sample-%s', uniqid());

        $builder = new MockObjectBuilder();

        /** @var Connection $connection */
        $connection = $builder->create(Connection::class, [
            new WithReturn('getParams', [], [
                'driver' => 'pdo_pgsql',
                'primary' => [
                    'url' => 'pgsql://root:root@localhost:5432?charset=utf8',
                    'dbname' => $dbName,
                ],
            ]),
            new WithoutReturn('close', []),
        ]);

        /** @var ConnectionProvider $connectionProvider */
        $connectionProvider = $builder->create(ConnectionProvider::class, [
            new WithReturn('getDefaultConnection', [], $connection),
        ]);

        /** @var AbstractPlatform $databasePlatform */
        $databasePlatform = $builder->create(AbstractPlatform::class, [
            new WithReturn('quoteSingleIdentifier', [$dbName], '"'.$dbName.'"'),
        ]);

        /** @var AbstractSchemaManager $schemaManager */
        $schemaManager = $builder->create(AbstractSchemaManager::class, [
            new WithReturn('listDatabases', [], []),
        ]);

        /** @var Connection $tmpConnection */
        $tmpConnection = $builder->create(Connection::class, [
            new WithReturn('createSchemaManager', [], $schemaManager),
            new WithReturn('getDatabasePlatform', [], $databasePlatform),
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
        $dbName = \sprintf('sample-%s', uniqid());

        $builder = new MockObjectBuilder();

        /** @var Connection $connection */
        $connection = $builder->create(Connection::class, [
            new WithReturn('getParams', [], [
                'driver' => 'pdo_pgsql',
                'primary' => [
                    'url' => 'pgsql://root:root@localhost:5432?charset=utf8',
                    'dbname' => $dbName,
                ],
            ]),
            new WithoutReturn('close', []),
        ]);

        /** @var ConnectionProvider $connectionProvider */
        $connectionProvider = $builder->create(ConnectionProvider::class, [
            new WithReturn('getDefaultConnection', [], $connection),
        ]);

        /** @var AbstractPlatform $databasePlatform */
        $databasePlatform = $builder->create(AbstractPlatform::class, [
            new WithReturn('quoteSingleIdentifier', [$dbName], '"'.$dbName.'"'),
        ]);

        /** @var AbstractSchemaManager $schemaManager */
        $schemaManager = $builder->create(AbstractSchemaManager::class, [
            new WithException(
                'dropDatabase',
                ['"'.$dbName.'"'],
                new \Exception(
                    'An exception occurred while executing a query: SQLSTATE[3D000]: Invalid catalog name: 7 ERROR:  database "'.$dbName.'" does not exist'
                )
            ),
        ]);

        /** @var Connection $tmpConnection */
        $tmpConnection = $builder->create(Connection::class, [
            new WithReturn('getDatabasePlatform', [], $databasePlatform),
            new WithReturn('createSchemaManager', [], $schemaManager),
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
