<?php

declare(strict_types=1);

namespace Jascha030\Config\Tests\Config\Traits;

use Jascha030\Config\Config\Traits\ConsoleApplicationConfigTrait;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;

/**
 * @internal
 */
#[CoversClass(ConsoleApplicationConfigTrait::class)]
final class ConsoleApplicationConfigTraitTest extends TestCase
{
    private static string $appName = 'testApplication';

    private static string $appVersion = '1.0.0';

    public function testGetName(): void
    {
        $mock = $this->createConsoleApplicationConfigTraitMock();

        $this->assertEquals(self::$appName, $mock->getName());
    }

    private function createConsoleApplicationConfigTraitMock(): object
    {
        return new class (new Application(self::$appName, self::$appVersion)) {
            use ConsoleApplicationConfigTrait;

            public function __construct(private readonly Application $application)
            {
            }

            private function getApplication(): Application
            {
                return $this->application;
            }
        };
    }
}
