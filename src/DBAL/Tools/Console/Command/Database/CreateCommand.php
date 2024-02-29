<?php

declare(strict_types=1);

namespace Chubbyphp\Laminas\Config\Doctrine\DBAL\Tools\Console\Command\Database;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Tools\Console\ConnectionProvider;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class CreateCommand extends Command
{
    private const RETURN_CODE_NOT_CREATE = 1;

    /**
     * @param \Closure(array $params): Connection $postParse
     */
    private ?\Closure $connectionFactory;

    public function __construct(private ConnectionProvider $connectionProvider, ?\Closure $connectionFactory = null)
    {
        parent::__construct();

        $this->connectionFactory = $connectionFactory ?? static fn (array $params) => DriverManager::getConnection($params);
    }

    protected function configure(): void
    {
        $this
            ->setName('dbal:database:create')
            ->setDescription('Creates the configured database')
            ->addOption('connection', null, InputOption::VALUE_REQUIRED, 'The named database connection')
            ->addOption('if-not-exists', null, InputOption::VALUE_NONE, 'No error, when the database already exists')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $connection = $this->getConnection($input);

        $params = $this->getParams($connection);

        $dbName = $this->getDbName($params);

        $isPath = isset($params['path']);

        // Need to get rid of _every_ occurrence of dbname from connection configuration
        unset($params['dbname'], $params['path'], $params['url']);

        $tmpConnection = ($this->connectionFactory)($params);

        $ifNotExists = $input->getOption('if-not-exists');

        $shouldNotCreateDatabase = $ifNotExists
            && \in_array($dbName, $tmpConnection->createSchemaManager()->listDatabases(), true);

        // Only quote if we don't have a path
        if (!$isPath) {
            $dbName = $tmpConnection->getDatabasePlatform()->quoteSingleIdentifier($dbName);
        }

        return $this->createDatabase($output, $tmpConnection, $dbName, $shouldNotCreateDatabase);
    }

    private function getConnection(InputInterface $input): Connection
    {
        $connectionName = $input->getOption('connection');
        \assert(\is_string($connectionName) || null === $connectionName);

        if (null !== $connectionName) {
            return $this->connectionProvider->getConnection($connectionName);
        }

        return $this->connectionProvider->getDefaultConnection();
    }

    /**
     * @return array<mixed>
     */
    private function getParams(Connection $connection): array
    {
        $params = $connection->getParams();
        if (isset($params['primary'])) {
            $params = $params['primary'];
        }

        return $params;
    }

    /**
     * @param array<string, string> $params
     */
    private function getDbName(array $params): string
    {
        if (isset($params['path'])) {
            return $params['path'];
        }

        if (isset($params['dbname'])) {
            return $params['dbname'];
        }

        throw new \InvalidArgumentException('Connection does not contain a \'path\' or \'dbname\' parameter.');
    }

    private function createDatabase(
        OutputInterface $output,
        Connection $tmpConnection,
        string $dbName,
        bool $shouldNotCreateDatabase
    ): int {
        try {
            if ($shouldNotCreateDatabase) {
                $output->writeln(
                    sprintf('<info>Database <comment>%s</comment> already exists. Skipped.</info>', $dbName)
                );
            } else {
                $tmpConnection->createSchemaManager()->createDatabase($dbName);
                $output->writeln(sprintf('<info>Created database <comment>%s</comment>.</info>', $dbName));
            }

            return 0;
        } catch (\Exception $exception) {
            $output->writeln(sprintf('<error>Could not create database <comment>%s</comment>.</error>', $dbName));
            $output->writeln(sprintf('<error>%s</error>', $exception->getMessage()));

            return self::RETURN_CODE_NOT_CREATE;
        }
    }
}
