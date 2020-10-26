<?php

declare(strict_types=1);

namespace Chubbyphp\Laminas\Config\Doctrine\DBAL\Tools\Console\Command\Database;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class CreateCommand extends Command
{
    private const RETURN_CODE_NOT_CREATE = 1;

    protected function configure(): void
    {
        $this
            ->setName('dbal:database:create')
            ->setDescription('Creates the configured database')
            ->addOption('if-not-exists', null, InputOption::VALUE_NONE, 'No error, when the database already exists')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var ConnectionHelper $helper */
        $helper = $this->getHelper('db');

        $connection = $helper->getConnection();

        $params = $this->getParams($connection);

        $dbName = $this->getDbName($params);

        $isPath = isset($params['path']);

        // Need to get rid of _every_ occurrence of dbname from connection configuration
        unset($params['dbname'], $params['path'], $params['url']);

        $tmpConnection = DriverManager::getConnection($params);

        $ifNotExists = $input->getOption('if-not-exists');

        $shouldNotCreateDatabase = $ifNotExists
            && in_array($dbName, $tmpConnection->getSchemaManager()->listDatabases());

        // Only quote if we don't have a path
        if (!$isPath) {
            $dbName = $tmpConnection->getDatabasePlatform()->quoteSingleIdentifier($dbName);
        }

        return $this->createDatabase($output, $tmpConnection, $dbName, $shouldNotCreateDatabase);
    }

    /**
     * @return array<mixed>
     */
    private function getParams(Connection $connection): array
    {
        $params = $connection->getParams();
        if (isset($params['master'])) {
            $params = $params['master'];
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
                $tmpConnection->getSchemaManager()->createDatabase($dbName);
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
