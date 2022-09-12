<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Laminas\Config\Doctrine\Unit\ServiceFactory\ORM\Tools\Console\Command\ClearCache;

use Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\ORM\Tools\Console\Command\ClearCache\ResultCommandFactory;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use Doctrine\ORM\Tools\Console\Command\ClearCache\ResultCommand;
use Doctrine\ORM\Tools\Console\EntityManagerProvider;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\ORM\Tools\Console\Command\ClearCache\ResultCommandFactory
 *
 * @internal
 */
final class ResultCommandFactoryTest extends TestCase
{
    use MockByCallsTrait;

    public function testInvoke(): void
    {
        /** @var EntityManagerProvider $entityManagerProvider */
        $entityManagerProvider = $this->getMockByCalls(EntityManagerProvider::class, []);

        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('has')->with(EntityManagerProvider::class)->willReturn(true),
            Call::create('get')->with(EntityManagerProvider::class)->willReturn($entityManagerProvider),
        ]);

        $factory = new ResultCommandFactory();

        $entityManagerCommand = $factory($container);

        self::assertInstanceOf(ResultCommand::class, $entityManagerCommand);
    }
}
