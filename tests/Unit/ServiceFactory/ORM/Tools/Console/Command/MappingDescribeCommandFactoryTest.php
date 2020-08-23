<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Laminas\Config\Doctrine\Unit\ServiceFactory\ORM\Tools\Console\Command;

use Chubbyphp\Laminas\Config\Doctrine\ORM\Tools\Console\Command\EntityManagerCommand;
use Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\ORM\Tools\Console\Command\MappingDescribeCommandFactory;
use Chubbyphp\Mock\MockByCallsTrait;
use Doctrine\ORM\Tools\Console\Command\MappingDescribeCommand;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\ORM\Tools\Console\Command\MappingDescribeCommandFactory
 *
 * @internal
 */
final class MappingDescribeCommandFactoryTest extends TestCase
{
    use MockByCallsTrait;

    public function testInvoke(): void
    {
        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class);

        $factory = new MappingDescribeCommandFactory();

        $entityManagerCommand = $factory($container);

        self::assertInstanceOf(EntityManagerCommand::class, $entityManagerCommand);

        $commandReflectionProperty = new \ReflectionProperty($entityManagerCommand, 'command');
        $commandReflectionProperty->setAccessible(true);

        self::assertInstanceOf(MappingDescribeCommand::class, $commandReflectionProperty->getValue($entityManagerCommand));
    }
}
