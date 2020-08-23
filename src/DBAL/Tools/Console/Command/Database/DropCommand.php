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

final class DropCommand extends Command
{
    private const RETURN_CODE_NOT_DROP = 1;
    private const RETURN_CODE_NO_FORCE = 2;

    protected function configure(): void
    {
        $this
            ->setName('dbal:database:drop')
            ->setDescription('Drops the configured database')
            ->addOption('if-exists', null, InputOption::VALUE_NONE, 'No error, when the database doesn\'t exist')
            ->addOption('force', null, InputOption::VALUE_NONE, 'Set this parameter to execute this action')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var ConnectionHelper $helper */
        $helper = $this->getHelper('db');

        $connection = $helper->getConnection();

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
        $connection = DriverManager::getConnection($params);

        $ifExists = $input->getOption('if-exists');

        $shouldDropDatabase = !$ifExists || in_array($dbName, $connection->getSchemaManager()->listDatabases());

        // Only quote if we don't have a path
        if (!$isPath) {
            $dbName = $connection->getDatabasePlatform()->quoteSingleIdentifier($dbName);
        }

        return $this->dropDatabase($output, $connection, $dbName, $shouldDropDatabase);
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
                $connection->getSchemaManager()->dropDatabase($dbName);
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
