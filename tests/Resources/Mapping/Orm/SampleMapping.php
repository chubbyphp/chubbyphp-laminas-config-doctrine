<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Laminas\Config\Doctrine\Resources\Mapping\Orm;

use Chubbyphp\Laminas\Config\Doctrine\Persistence\Mapping\Driver\ClassMapMappingInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\ClassMetadata as ORMClassMetadata;
use Doctrine\Persistence\Mapping\ClassMetadata;

final class SampleMapping implements ClassMapMappingInterface
{
    /**
     * @param ORMClassMetadata $metadata
     */
    public function configureMapping(ClassMetadata $metadata): void
    {
        $builder = new ClassMetadataBuilder($metadata);
        $builder->setTable('sample');
        $builder->createField('id', Types::GUID)->makePrimaryKey()->build();
        $builder->addField('createdAt', Types::DATETIME_IMMUTABLE);
        $builder->addField('updatedAt', Types::DATETIME_IMMUTABLE, ['nullable' => true]);
        $builder->addField('name', Types::STRING);
    }
}
