<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Laminas\Config\Doctrine\Integration;

use Chubbyphp\Laminas\Config\Config;
use Chubbyphp\Laminas\Config\ContainerFactory;
use Chubbyphp\Laminas\Config\Doctrine\ODM\MongoDB\Tools\Console\Command\DocumentManagerCommand;
use Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\Common\Cache\ArrayCacheFactory;
use Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\ODM\MongoDB\DocumentManagerFactory;
use Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\Persistence\Mapping\Driver\ClassMapDriverFactory;
use Chubbyphp\Tests\Laminas\Config\Doctrine\Resources\Mapping\MongodbOdm\SampleMapping;
use Chubbyphp\Tests\Laminas\Config\Doctrine\Resources\Model\Sample;
use Doctrine\Common\Cache\Cache;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Tools\Console\Command\Schema\CreateCommand;
use Doctrine\Persistence\Mapping\Driver\MappingDriver;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * @internal
 * @coversNothing
 */
final class DocumentManagerTest extends TestCase
{
    public function test(): void
    {
        $config = [
            'dependencies' => [
                'factories' => [
                    Cache::class => ArrayCacheFactory::class,
                    DocumentManager::class => DocumentManagerFactory::class,
                    MappingDriver::class => ClassMapDriverFactory::class,
                ],
            ],
            'doctrine' => [
                'cache' => [
                    'array' => [
                        'namespace' => 'doctrine',
                    ],
                ],
                'driver' => [
                    'classMap' => [
                        'map' => [
                            Sample::class => SampleMapping::class,
                        ],
                    ],
                ],
                'mongodb' => [
                    'client' => [
                        'uri' => getenv('MONGODB_URI') ? getenv('MONGODB_URI') : 'mongodb://root:root@localhost:27017',
                        'driverOptions' => [
                            'typeMap' => DocumentManager::CLIENT_TYPEMAP,
                            'driver' => [
                                'name' => 'doctrine-odm',
                            ],
                        ],
                    ],
                ],
                'mongodbOdm' => [
                    'configuration' => [
                        'defaultDB' => 'sample',
                        'hydratorDir' => '/tmp/doctrine/mongodbOdm/hydrators',
                        'hydratorNamespace' => 'DoctrineMongoDBODMHydrators',
                        'metadataCacheImpl' => Cache::class,
                        'metadataDriverImpl' => MappingDriver::class,
                        'proxyDir' => '/tmp/doctrine/mongodbOdm/proxies',
                        'proxyNamespace' => 'DoctrineMongoDBODMProxy',
                    ],
                ],
            ],
        ];

        $factory = new ContainerFactory();

        $container = $factory(new Config($config));

        /** @var DocumentManager $documentManager */
        $documentManager = $container->get(DocumentManager::class);

        $output = new BufferedOutput();

        $command = new DocumentManagerCommand(new CreateCommand(), $container);
        $command->run(new ArrayInput([]), $output);

        $sample = new Sample();
        $sample->setName('name');

        $documentManager->persist($sample);
        $documentManager->flush();

        /** @var Sample $sampleFromDatabase */
        $sampleFromDatabase = $documentManager->find(Sample::class, $sample->getId());

        self::assertInstanceOf(Sample::class, $sampleFromDatabase);
        self::assertSame($sample->getId(), $sampleFromDatabase->getId());
    }
}
