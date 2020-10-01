<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Laminas\Config\Doctrine\Resources\Mapping\MongodbOdm;

use Chubbyphp\Laminas\Config\Doctrine\Persistence\Mapping\Driver\ClassMapMappingInterface;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadata as MongodbODMClassMetadata;
use Doctrine\ODM\MongoDB\Types\Type;
use Doctrine\Persistence\Mapping\ClassMetadata;

final class SampleMapping implements ClassMapMappingInterface
{
    /**
     * @param MongodbODMClassMetadata $metadata
     */
    public function configureMapping(ClassMetadata $metadata): void
    {
        $metadata->setCollection('sample');
        $metadata->mapField(['name' => 'id', 'id' => true, 'strategy' => 'none']);
        $metadata->mapField(['name' => 'createdAt', 'type' => Type::DATE]);
        $metadata->mapField(['name' => 'updatedAt', 'type' => Type::DATE, 'nullable' => true]);
        $metadata->mapField(['name' => 'name', 'type' => Type::STRING]);
    }
}
