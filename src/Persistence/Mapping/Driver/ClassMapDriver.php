<?php

declare(strict_types=1);

namespace Chubbyphp\Laminas\Config\Doctrine\Persistence\Mapping\Driver;

use Doctrine\Persistence\Mapping\ClassMetadata;
use Doctrine\Persistence\Mapping\Driver\MappingDriver;
use Doctrine\Persistence\Mapping\MappingException;

final class ClassMapDriver implements MappingDriver
{
    /**
     * @param array<string, string> $map
     */
    public function __construct(private array $map) {}

    /**
     * @param string $className
     *
     * @throws MappingException
     */
    public function loadMetadataForClass($className, ClassMetadata $metadata): void
    {
        if (!isset($this->map[$className])) {
            throw new MappingException(\sprintf("Missing mapping for class '%s'", $className));
        }

        $mappingClassName = $this->map[$className];

        /** @var ClassMapMappingInterface $mapping */
        $mapping = new $mappingClassName();
        $mapping->configureMapping($metadata);
    }

    /**
     * @return array<string>
     */
    public function getAllClassNames(): array
    {
        return array_keys($this->map);
    }

    /**
     * @param string $className
     */
    public function isTransient($className): bool
    {
        return !(isset($this->map[$className]));
    }
}
