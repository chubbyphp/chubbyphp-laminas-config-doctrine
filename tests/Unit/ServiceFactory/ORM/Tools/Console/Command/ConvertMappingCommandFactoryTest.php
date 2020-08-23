<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Laminas\Config\Doctrine\Unit\ServiceFactory\ORM\Tools\Console\Command;

use Chubbyphp\Laminas\Config\Doctrine\ORM\Tools\Console\Command\EntityManagerCommand;
use Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\ORM\Tools\Console\Command\ConvertMappingCommandFactory;
use Chubbyphp\Mock\MockByCallsTrait;
use Doctrine\ORM\Tools\Console\Command\ConvertMappingCommand;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\ORM\Tools\Console\Command\ConvertMappingCommandFactory
 *
 * @internal
 */
final class ConvertMappingCommandFactoryTest extends TestCase
{
    use MockByCallsTrait;

    public function testInvoke(): void
    {
        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class);

        $factory = new ConvertMappingCommandFactory();

        $entityManagerCommand = $factory($container);

        self::assertInstanceOf(EntityManagerCommand::class, $entityManagerCommand);

        $commandReflectionProperty = new \ReflectionProperty($entityManagerCommand, 'command');
        $commandReflectionProperty->setAccessible(true);

        self::assertInstanceOf(ConvertMappingCommand::class, $commandReflectionProperty->getValue($entityManagerCommand));
    }
}
