<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Laminas\Config\Doctrine\Unit\ServiceFactory\ORM\Tools\Console\Command\SchemaTool;

use Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\ORM\Tools\Console\Command\SchemaTool\UpdateCommandFactory;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use Doctrine\ORM\Tools\Console\Command\SchemaTool\UpdateCommand;
use Doctrine\ORM\Tools\Console\EntityManagerProvider;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\ORM\Tools\Console\Command\SchemaTool\UpdateCommandFactory
 *
 * @internal
 */
final class UpdateCommandFactoryTest extends TestCase
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

        $factory = new UpdateCommandFactory();

        $entityManagerCommand = $factory($container);

        self::assertInstanceOf(UpdateCommand::class, $entityManagerCommand);
    }
}
