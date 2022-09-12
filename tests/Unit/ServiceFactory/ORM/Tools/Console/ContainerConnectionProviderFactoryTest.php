<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Laminas\Config\Doctrine\Unit\ServiceFactory\ORM\Tools\Console;

use Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\ORM\Tools\Console\ContainerEntityManagerProviderFactory;
use Chubbyphp\Mock\MockByCallsTrait;
use Doctrine\ORM\Tools\Console\EntityManagerProvider;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\ORM\Tools\Console\ContainerEntityManagerProviderFactory
 *
 * @internal
 */
final class ContainerConnectionProviderFactoryTest extends TestCase
{
    use MockByCallsTrait;

    public function testInvoke(): void
    {
        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class);

        $factory = new ContainerEntityManagerProviderFactory();

        $entityManagerCommand = $factory($container);

        self::assertInstanceOf(EntityManagerProvider::class, $entityManagerCommand);
    }
}
