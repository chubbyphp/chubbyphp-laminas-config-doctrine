<?php

declare(strict_types=1);

namespace Chubbyphp\Laminas\Config\Doctrine\Persistence\Mapping\Driver;

use Doctrine\Persistence\Mapping\ClassMetadata;

interface ClassMapMappingInterface
{
    /**
     * @param ClassMetadata<object> $metadata
     */
    public function configureMapping(ClassMetadata $metadata): void;
}
