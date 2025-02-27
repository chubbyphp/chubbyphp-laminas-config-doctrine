<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Laminas\Config\Doctrine\Unit\DBAL\Tools\Console\Command\Database;

use Chubbyphp\Laminas\Config\Doctrine\DBAL\Tools\Console\Command\Database\CreateCommand;
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
 * @covers \Chubbyphp\Laminas\Config\Doctrine\DBAL\Tools\Console\Command\Database\CreateCommand
 *
 * @internal
 */
final class CreateCommandTest extends TestCase
{
    public function testExecuteFakeSqlite(): void
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
            new WithoutReturn('createDatabase', [$path]),
        ]);

        /** @var Connection $tmpConnection */
        $tmpConnection = $builder->create(Connection::class, [
            new WithReturn('createSchemaManager', [], $schemaManager),
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
        $this->expectExceptionMessage("Connection does not contain a 'path' or 'dbname' parameter.");

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

        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $command = new CreateCommand($connectionProvider, static fn (array $params) => null);

        $command->run($input, $output);
    }

    public function testExecuteFakePgsql(): void
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
            new WithoutReturn('createDatabase', ['"'.$dbName.'"']),
        ]);

        /** @var Connection $tmpConnection */
        $tmpConnection = $builder->create(Connection::class, [
            new WithReturn('getDatabasePlatform', [], $databasePlatform),
            new WithReturn('createSchemaManager', [], $schemaManager),
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
            new WithException(
                'createDatabase',
                ['"'.$dbName.'"'],
                new \Exception(
                    'An exception occurred while executing a query: SQLSTATE[42P04]: Duplicate database: 7 ERROR:  database "'.$dbName.'" already exists'
                )
            ),
        ]);

        /** @var Connection $tmpConnection */
        $tmpConnection = $builder->create(Connection::class, [
            new WithReturn('getDatabasePlatform', [], $databasePlatform),
            new WithReturn('createSchemaManager', [], $schemaManager),
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
            new WithReturn('listDatabases', [], [$dbName]),
        ]);

        /** @var Connection $tmpConnection */
        $tmpConnection = $builder->create(Connection::class, [
            new WithReturn('createSchemaManager', [], $schemaManager),
            new WithReturn('getDatabasePlatform', [], $databasePlatform),
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
