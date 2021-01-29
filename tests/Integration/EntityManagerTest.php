<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Laminas\Config\Doctrine\Integration;

use Chubbyphp\Laminas\Config\Config;
use Chubbyphp\Laminas\Config\ContainerFactory;
use Chubbyphp\Laminas\Config\Doctrine\DBAL\Tools\Console\Command\Database\CreateCommand as DatabaseCreateCommand;
use Chubbyphp\Laminas\Config\Doctrine\ORM\Tools\Console\Command\EntityManagerCommand;
use Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\Common\Cache\ArrayCacheFactory;
use Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\ORM\EntityManagerFactory;
use Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\Persistence\Mapping\Driver\ClassMapDriverFactory;
use Chubbyphp\Tests\Laminas\Config\Doctrine\Resources\Mapping\Orm\SampleMapping;
use Chubbyphp\Tests\Laminas\Config\Doctrine\Resources\Model\Sample;
use Doctrine\Common\Cache\Cache;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Console\Command\SchemaTool\UpdateCommand as SchemaUpdateCommand;
use Doctrine\Persistence\Mapping\Driver\MappingDriver;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * @internal
 * @coversNothing
 */
final class EntityManagerTest extends TestCase
{
    public function test(): void
    {
        $config = [
            'dependencies' => [
                'factories' => [
                    Cache::class => ArrayCacheFactory::class,
                    EntityManager::class => EntityManagerFactory::class,
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
                        'driver' => 'pdo_pgsql',
                        'charset' => 'utf8',
                        'user' => 'root',
                        'password' => 'root',
                        'host' => 'localhost',
                        'port' => 5432,
                        'dbname' => 'sample',
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
                        'metadataCacheImpl' => Cache::class,
                        'metadataDriverImpl' => MappingDriver::class,
                        'proxyDir' => '/tmp/doctrine/orm/proxies',
                        'proxyNamespace' => 'DoctrineORMProxy',
                    ],
                ],
            ],
        ];

        $factory = new ContainerFactory();

        $container = $factory(new Config($config));

        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);

        $output = new BufferedOutput();

        $command = new EntityManagerCommand(new DatabaseCreateCommand(), $container);
        $command->run(new ArrayInput(['--if-not-exists' => true]), $output);

        $command = new EntityManagerCommand(new SchemaUpdateCommand(), $container);
        $command->run(new ArrayInput(['--dump-sql' => true, '--force' => true]), $output);

        $sample = new Sample();
        $sample->setName('name');

        $entityManager->persist($sample);
        $entityManager->flush();

        /** @var Sample $sampleFromDatabase */
        $sampleFromDatabase = $entityManager->find(Sample::class, $sample->getId());

        self::assertInstanceOf(Sample::class, $sampleFromDatabase);
        self::assertSame($sample->getId(), $sampleFromDatabase->getId());
    }
}
