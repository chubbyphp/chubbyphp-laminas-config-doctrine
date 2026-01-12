<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Laminas\Config\Doctrine\Integration;

use Chubbyphp\Laminas\Config\Config;
use Chubbyphp\Laminas\Config\ContainerFactory;
use Chubbyphp\Laminas\Config\Doctrine\DBAL\Tools\Console\Command\Database\CreateCommand as DatabaseCreateCommand;
use Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\Common\Cache\ArrayAdapterFactory;
use Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\DBAL\ConnectionFactory;
use Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\DBAL\Tools\Console\ContainerConnectionProviderFactory;
use Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\ORM\EntityManagerFactory;
use Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\ORM\Tools\Console\ContainerEntityManagerProviderFactory;
use Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\Persistence\Mapping\Driver\ClassMapDriverFactory;
use Chubbyphp\Tests\Laminas\Config\Doctrine\Resources\Mapping\Orm\SampleMapping;
use Chubbyphp\Tests\Laminas\Config\Doctrine\Resources\Model\Sample;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Tools\Console\ConnectionProvider;
use Doctrine\DBAL\Tools\DsnParser;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Console\Command\SchemaTool\UpdateCommand as SchemaUpdateCommand;
use Doctrine\ORM\Tools\Console\EntityManagerProvider;
use Doctrine\Persistence\Mapping\Driver\MappingDriver;
use PHPUnit\Framework\TestCase;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * @internal
 *
 * @coversNothing
 */
final class EntityManagerTest extends TestCase
{
    public function test(): void
    {
        $dsnParser = new DsnParser(['pgsql' => 'pdo_pgsql']);
        $connectionParams = $dsnParser
            ->parse(getenv('POSTGRES_URL') ?: 'pgsql://root:root@localhost:5432?charset=UTF8')
        ;

        $config = [
            'dependencies' => [
                'factories' => [
                    CacheItemPoolInterface::class => ArrayAdapterFactory::class,
                    Connection::class => ConnectionFactory::class,
                    ConnectionProvider::class => ContainerConnectionProviderFactory::class,
                    EntityManagerInterface::class => EntityManagerFactory::class,
                    EntityManagerProvider::class => ContainerEntityManagerProviderFactory::class,
                    MappingDriver::class => ClassMapDriverFactory::class,
                ],
            ],
            'doctrine' => [
                'cache' => [
                    'array' => [
                        'namespace' => 'doctrine',
                    ],
                ],
                'dbal' => [
                    'connection' => [
                        ...$connectionParams,
                        'dbname' => $connectionParams['dbname'] ?? 'sample',
                    ],
                ],
                'driver' => [
                    'classMap' => [
                        'map' => [
                            Sample::class => SampleMapping::class,
                        ],
                    ],
                ],
                'orm' => [
                    'configuration' => [
                        'metadataCache' => CacheItemPoolInterface::class,
                        'metadataDriverImpl' => MappingDriver::class,
                        'proxyDir' => '/tmp/doctrine/orm/proxies',
                        'proxyNamespace' => 'DoctrineORMProxy',
                    ],
                ],
            ],
        ];

        $factory = new ContainerFactory();

        $container = $factory(new Config($config));

        /** @var ConnectionProvider $connectionProvider */
        $connectionProvider = $container->get(ConnectionProvider::class);

        /** @var EntityManagerProvider $entityManagerProvider */
        $entityManagerProvider = $container->get(EntityManagerProvider::class);

        $output = new BufferedOutput();

        $command = new DatabaseCreateCommand($connectionProvider);
        $command->run(new ArrayInput(['--if-not-exists' => true]), $output);

        $command = new SchemaUpdateCommand($entityManagerProvider);
        $command->run(new ArrayInput(['--dump-sql' => true, '--force' => true]), $output);

        $sample = new Sample();
        $sample->setName('name');

        $entityManager = $entityManagerProvider->getDefaultManager();

        $entityManager->persist($sample);
        $entityManager->flush();

        /** @var Sample $sampleFromDatabase */
        $sampleFromDatabase = $entityManager->find(Sample::class, $sample->getId());

        self::assertInstanceOf(Sample::class, $sampleFromDatabase);
        self::assertSame($sample->getId(), $sampleFromDatabase->getId());
    }
}
