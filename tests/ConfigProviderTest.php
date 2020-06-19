<?php

declare(strict_types=1);

namespace TokenTest;

use PHPUnit\Framework\TestCase;
use Token\ConfigProvider;

class ConfigProviderTest extends TestCase
{
    public function testConfigProvider()
    {
        $provider = new ConfigProvider();
        $this->assertArrayHasKey('dependencies', $provider());
        $this->assertArrayHasKey('publish', $provider());
    }
}