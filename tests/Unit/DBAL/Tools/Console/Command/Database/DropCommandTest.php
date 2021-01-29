<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Laminas\Config\Doctrine\Unit\DBAL\Tools\Console\Command\Database;

use Chubbyphp\Laminas\Config\Doctrine\DBAL\Tools\Console\Command\Database\DropCommand;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Helper\HelperSet;
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

        $setupConnection->getSchemaManager()->createDatabase($path);

        /** @var Connection|MockObject $connection */
        $connection = $this->getMockByCalls(Connection::class, [
            Call::create('getParams')->with()->willReturn([
                'driver' => 'pdo_sqlite',
                'path' => $path,
            ]),
            Call::create('close'),
        ]);

        $input = new ArrayInput([
            '--force' => true,
        ]);

        $output = new BufferedOutput();

        $command = new DropCommand();
        $command->setHelperSet(new HelperSet([
            'db' => new ConnectionHelper($connection),
        ]));

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

        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $command = new DropCommand();
        $command->setHelperSet(new HelperSet([
            'db' => new ConnectionHelper($connection),
        ]));

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

        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $command = new DropCommand();
        $command->setHelperSet(new HelperSet([
            'db' => new ConnectionHelper($connection),
        ]));

        $command->run($input, $output);
    }

    public function testExecutePgsqlWithName(): void
    {
        $dbName = sprintf('sample-%s', uniqid());

        $setupConnection = DriverManager::getConnection([
            'driver' => 'pdo_pgsql',
            'charset' => 'utf8',
            'user' => 'root',
            'password' => 'root',
            'host' => 'localhost',
            'port' => 5432,
        ]);

        $setupConnection->getSchemaManager()->createDatabase('"'.$dbName.'"');

        /** @var Connection|MockObject $connection */
        $connection = $this->getMockByCalls(Connection::class, [
            Call::create('getParams')->with()->willReturn([
                'master' => [
                    'driver' => 'pdo_pgsql',
                    'charset' => 'utf8',
                    'user' => 'root',
                    'password' => 'root',
                    'host' => 'localhost',
                    'port' => 5432,
                    'dbname' => $dbName,
                ],
            ]),
            Call::create('close'),
        ]);

        $input = new ArrayInput([
            '--force' => true,
        ]);

        $output = new BufferedOutput();

        $command = new DropCommand();
        $command->setHelperSet(new HelperSet([
            'db' => new ConnectionHelper($connection),
        ]));

        self::assertSame(0, $command->run($input, $output));

        self::assertSame(str_replace('dbname', $dbName, 'Dropped database "dbname".'.PHP_EOL), $output->fetch());
    }

    public function testExecutePgsqlWithNameAndMissingDatabaseIfExists(): void
    {
        $dbName = sprintf('sample-%s', uniqid());

        /** @var Connection|MockObject $connection */
        $connection = $this->getMockByCalls(Connection::class, [
            Call::create('getParams')->with()->willReturn([
                'master' => [
                    'driver' => 'pdo_pgsql',
                    'charset' => 'utf8',
                    'user' => 'root',
                    'password' => 'root',
                    'host' => 'localhost',
                    'port' => 5432,
                    'dbname' => $dbName,
                ],
            ]),
            Call::create('close'),
        ]);

        $input = new ArrayInput([
            '--if-exists' => true,
            '--force' => true,
        ]);

        $output = new BufferedOutput();

        $command = new DropCommand();
        $command->setHelperSet(new HelperSet([
            'db' => new ConnectionHelper($connection),
        ]));

        self::assertSame(0, $command->run($input, $output));

        self::assertSame(
            str_replace('dbname', $dbName, 'Database "dbname" doesn\'t exist. Skipped.'.PHP_EOL),
            $output->fetch()
        );
    }

    public function testExecutePgsqlWithNameAndMissingDatabase(): void
    {
        $dbName = sprintf('sample-%s', uniqid());

        /** @var Connection|MockObject $connection */
        $connection = $this->getMockByCalls(Connection::class, [
            Call::create('getParams')->with()->willReturn([
                'master' => [
                    'driver' => 'pdo_pgsql',
                    'charset' => 'utf8',
                    'user' => 'root',
                    'password' => 'root',
                    'host' => 'localhost',
                    'port' => 5432,
                    'dbname' => $dbName,
                ],
            ]),
            Call::create('close'),
        ]);

        $input = new ArrayInput([
            '--force' => true,
        ]);

        $output = new BufferedOutput();

        $command = new DropCommand();
        $command->setHelperSet(new HelperSet([
            'db' => new ConnectionHelper($connection),
        ]));

        self::assertSame(1, $command->run($input, $output));

        $message = <<<'EOT'
Could not drop database "dbname".
An exception occurred while executing 'DROP DATABASE "dbname"':

EOT;

        self::assertStringStartsWith(str_replace('dbname', $dbName, $message), $output->fetch());
    }
}
