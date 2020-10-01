<?php

declare(strict_types=1);

namespace Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\ODM\MongoDB\Tools\Console\Command;

use Chubbyphp\Laminas\Config\Doctrine\ODM\MongoDB\Tools\Console\Command\DocumentManagerCommand;
use Doctrine\ODM\MongoDB\Tools\Console\Command\GeneratePersistentCollectionsCommand;
use Psr\Container\ContainerInterface;

final class GeneratePersistentCollectionsCommandFactory
{
    public function __invoke(ContainerInterface $container): DocumentManagerCommand
    {
        return new DocumentManagerCommand(new GeneratePersistentCollectionsCommand(), $container);
    }
}
