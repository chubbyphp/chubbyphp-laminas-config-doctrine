<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Laminas\Config\Doctrine\Unit\Persistence\Mapping\Driver;

use Chubbyphp\Laminas\Config\Doctrine\Persistence\Mapping\Driver\ClassMapDriver;
use Chubbyphp\Laminas\Config\Doctrine\Persistence\Mapping\Driver\ClassMapMappingInterface;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use Doctrine\Persistence\Mapping\ClassMetadata;
use Doctrine\Persistence\Mapping\MappingException;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Laminas\Config\Doctrine\Persistence\Mapping\Driver\ClassMapDriver
 *
 * @internal
 */
final class ClassMapDriverTest extends TestCase
{
    use MockByCallsTrait;

    public function testLoadMetadataForClassWithoutExistingMapping(): void
    {
        $this->expectException(MappingException::class);
        $this->expectExceptionMessage("Missing mapping for class 'stdClass'");

        /** @var ClassMetadata $classMetadata */
        $classMetadata = $this->getMockByCalls(ClassMetadata::class);

        $mappingDriver = new ClassMapDriver([]);
        $mappingDriver->loadMetadataForClass(\stdClass::class, $classMetadata);
    }

    public function testLoadMetadataForClassWithExistingMapping(): void
    {
        $modelMapping = new class() implements ClassMapMappingInterface {
            public function configureMapping(ClassMetadata $metadata): void
            {
                Assert::assertSame('name', $metadata->getName());
            }
        };

        /** @var ClassMetadata $classMetadata */
        $classMetadata = $this->getMockByCalls(ClassMetadata::class, [
            Call::create('getName')->with()->willReturn('name'),
        ]);

        $mappingDriver = new ClassMapDriver([\stdClass::class => \get_class($modelMapping)]);
        $mappingDriver->loadMetadataForClass(\stdClass::class, $classMetadata);
    }

    public function testGetAllClassNames(): void
    {
        $mappingDriver = new ClassMapDriver([\stdClass::class => 'someMappingClass']);

        self::assertSame([\stdClass::class], $mappingDriver->getAllClassNames());
    }

    public function testIsTransient(): void
    {
        $mappingDriver = new ClassMapDriver([\stdClass::class => 'someMappingClass']);

        self::assertFalse($mappingDriver->isTransient(\stdClass::class));
        self::assertTrue($mappingDriver->isTransient('unknownClass'));
    }
}
