<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Laminas\Config\Doctrine\Unit\ORM\Tools\Console;

use Chubbyphp\Laminas\Config\Doctrine\ORM\Tools\Console\ContainerEntityManagerProvider;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \Chubbyphp\Laminas\Config\Doctrine\ORM\Tools\Console\ContainerEntityManagerProvider
 *
 * @internal
 */
final class ContainerEntityManagerProviderTest extends TestCase
{
    use MockByCallsTrait;

    public function testGetDefaultManager(): void
    {
        /** @var EntityManagerInterface|MockObject $entityManager */
        $entityManager = $this->getMockByCalls(EntityManagerInterface::class);

        /** @var ContainerInterface|MockObject $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with(EntityManagerInterface::class)->willReturn($entityManager),
        ]);

        $connectionProvider = new ContainerEntityManagerProvider($container);

        self::assertSame($connectionProvider->getDefaultManager(), $entityManager);
    }

    public function testGetManager(): void
    {
        /** @var EntityManagerInterface|MockObject $entityManager */
        $entityManager = $this->getMockByCalls(EntityManagerInterface::class);

        /** @var ContainerInterface|MockObject $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with(EntityManagerInterface::class.'name')->willReturn($entityManager),
        ]);

        $connectionProvider = new ContainerEntityManagerProvider($container);

        self::assertSame($connectionProvider->getManager('name'), $entityManager);
    }
}
