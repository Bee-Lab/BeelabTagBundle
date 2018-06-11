<?php

namespace Beelab\TagBundle\Tests\DependencyInjection;

use Beelab\TagBundle\DependencyInjection\Configuration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

final class ConfigurationTest extends TestCase
{
    public function testGetConfigTreeBuilder(): void
    {
        $configuration = new Configuration();
        $this->assertInstanceOf(TreeBuilder::class, $configuration->getConfigTreeBuilder());
    }
}
