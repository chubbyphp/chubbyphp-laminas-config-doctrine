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

final class DropCommand extends Command
{
    private const RETURN_CODE_NOT_DROP = 1;
    private const RETURN_CODE_NO_FORCE = 2;

    /**
     * @param \Closure(array $params): Connection $postParse
     */
    private null|\Closure $connectionFactory;

    public function __construct(private ConnectionProvider $connectionProvider, null|\Closure $connectionFactory = null)
    {
        parent::__construct();

        $this->connectionFactory = $connectionFactory ?? static fn (array $params) => DriverManager::getConnection($params);
    }

    protected function configure(): void
    {
        $this
            ->setName('dbal:database:drop')
            ->setDescription('Drops the configured database')
            ->addOption('connection', null, InputOption::VALUE_REQUIRED, 'The named database connection')
            ->addOption('if-exists', null, InputOption::VALUE_NONE, 'No error, when the database doesn\'t exist')
            ->addOption('force', null, InputOption::VALUE_NONE, 'Set this parameter to execute this action')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $connection = $this->getConnection($input);

        $params = $this->getParams($connection);

        $dbName = $this->getDbName($params);

        if (!$input->getOption('force')) {
            $this->writeMissingForceOutput($output, $dbName);

            return self::RETURN_CODE_NO_FORCE;
        }

        $isPath = isset($params['path']);

        // Need to get rid of _every_ occurrence of dbname from connection configuration
        unset($params['dbname'], $params['path'], $params['url']);

        $connection->close();
        $tmpConnection = ($this->connectionFactory)($params);

        $ifExists = $input->getOption('if-exists');

        $shouldDropDatabase = !$ifExists || \in_array($dbName, $tmpConnection->createSchemaManager()->listDatabases(), true);

        // Only quote if we don't have a path
        if (!$isPath) {
            $dbName = $tmpConnection->getDatabasePlatform()->quoteSingleIdentifier($dbName);
        }

        return $this->dropDatabase($output, $tmpConnection, $dbName, $shouldDropDatabase);
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

    private function writeMissingForceOutput(OutputInterface $output, string $dbName): void
    {
        $output->writeln(
            '<error>ATTENTION:</error> This operation should not be executed in a production environment.'
        );
        $output->writeln('');
        $output->writeln(sprintf('<info>Would drop the database <comment>%s</comment>.</info>', $dbName));
        $output->writeln('Please run the operation with --force to execute');
        $output->writeln('<error>All data will be lost!</error>');
    }

    private function dropDatabase(
        OutputInterface $output,
        Connection $connection,
        string $dbName,
        bool $shouldDropDatabase
    ): int {
        try {
            if ($shouldDropDatabase) {
                $connection->createSchemaManager()->dropDatabase($dbName);
                $output->writeln(sprintf('<info>Dropped database <comment>%s</comment>.</info>', $dbName));
            } else {
                $output->writeln(
                    sprintf('<info>Database <comment>%s</comment> doesn\'t exist. Skipped.</info>', $dbName)
                );
            }
        } catch (\Exception $exception) {
            $output->writeln(sprintf('<error>Could not drop database <comment>%s</comment>.</error>', $dbName));
            $output->writeln(sprintf('<error>%s</error>', $exception->getMessage()));

            return self::RETURN_CODE_NOT_DROP;
        }

        return 0;
    }
}
