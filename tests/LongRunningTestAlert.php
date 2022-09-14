<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Laminas\Config\Doctrine;

use PHPUnit\Runner\AfterLastTestHook;
use PHPUnit\Runner\AfterTestHook;

/**
 * @see https://www.aaronsaray.com/2021/finding-slow-tests-in-phpunit-9
 */
final class LongRunningTestAlert implements AfterLastTestHook, AfterTestHook
{
    private const MAX_SECONDS_ALLOWED = 1;

    private array $longRunningTests = [];

    public function executeAfterTest(string $test, float $time): void
    {
        if ($time > self::MAX_SECONDS_ALLOWED) {
            $this->longRunningTests[] = sprintf('The %s test took %s seconds!', $test, $time);
        }
    }

    public function executeAfterLastTest(): void
    {
        if ([] !== $this->longRunningTests) {
            fwrite(STDERR, PHP_EOL.PHP_EOL.implode(PHP_EOL, $this->longRunningTests));
        }
    }
}
