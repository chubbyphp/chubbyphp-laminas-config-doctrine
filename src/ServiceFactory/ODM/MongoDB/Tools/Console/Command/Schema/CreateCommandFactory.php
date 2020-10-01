<?php

declare(strict_types=1);

namespace Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\ODM\MongoDB\Tools\Console\Command\Schema;

use Chubbyphp\Laminas\Config\Doctrine\ODM\MongoDB\Tools\Console\Command\DocumentManagerCommand;
use Doctrine\ODM\MongoDB\Tools\Console\Command\Schema\CreateCommand;
use Psr\Container\ContainerInterface;

final class CreateCommandFactory
{
    public function __invoke(ContainerInterface $container): DocumentManagerCommand
    {
        return new DocumentManagerCommand(new CreateCommand(), $container);
    }
}
