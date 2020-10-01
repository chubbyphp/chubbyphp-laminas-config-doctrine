<?php

declare(strict_types=1);

namespace Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\ODM\MongoDB\Tools\Console\Command\Schema;

use Chubbyphp\Laminas\Config\Doctrine\ODM\MongoDB\Tools\Console\Command\DocumentManagerCommand;
use Doctrine\ODM\MongoDB\Tools\Console\Command\Schema\ValidateCommand;
use Psr\Container\ContainerInterface;

final class ValidateCommandFactory
{
    public function __invoke(ContainerInterface $container): DocumentManagerCommand
    {
        return new DocumentManagerCommand(new ValidateCommand(), $container);
    }
}
