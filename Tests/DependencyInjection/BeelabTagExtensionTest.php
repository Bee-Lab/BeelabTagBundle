<?php

namespace Beelab\TagBundle\Tests\DependencyInjection;

use Beelab\TagBundle\DependencyInjection\BeelabTagExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

final class BeelabTagExtensionTest extends TestCase
{
    public function testLoadSetParameters(): void
    {
        /** @var ContainerBuilder&\PHPUnit\Framework\MockObject\MockObject $container */
        $container = $this->getMockBuilder(ContainerBuilder::class)->disableOriginalConstructor()->getMock();
        /** @var ParameterBag&\PHPUnit\Framework\MockObject\MockObject $parameterBag */
        $parameterBag = $this->getMockBuilder(ParameterBag::class)->disableOriginalConstructor()->getMock();

        $parameterBag->expects($this->any())->method('add');

        $container->expects($this->any())->method('getParameterBag')->willReturn($parameterBag);

        $extension = new BeelabTagExtension();
        $configs = [
            ['tag_class' => 'foo'],
            ['purge' => false],
        ];
        $extension->load($configs, $container);
        $this->assertTrue(true);
    }
}
