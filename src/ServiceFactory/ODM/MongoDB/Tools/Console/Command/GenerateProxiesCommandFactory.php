<?php

declare(strict_types=1);

namespace Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\ODM\MongoDB\Tools\Console\Command;

use Chubbyphp\Laminas\Config\Doctrine\ODM\MongoDB\Tools\Console\Command\DocumentManagerCommand;
use Doctrine\ODM\MongoDB\Tools\Console\Command\GenerateProxiesCommand;
use Psr\Container\ContainerInterface;

final class GenerateProxiesCommandFactory
{
    public function __invoke(ContainerInterface $container): DocumentManagerCommand
    {
        return new DocumentManagerCommand(new GenerateProxiesCommand(), $container);
    }
}
