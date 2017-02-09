<?php

namespace Beelab\TagBundle\Tests\DependencyInjection;

use Beelab\TagBundle\DependencyInjection\BeelabTagExtension;
use PHPUnit\Framework\TestCase;

class BeelabTagExtensionTest extends TestCase
{
    public function testLoadSetParameters()
    {
        $container = $this->getMockBuilder('Symfony\\Component\\DependencyInjection\\ContainerBuilder')
            ->disableOriginalConstructor()->getMock();
        $parameterBag = $this->getMockBuilder('Symfony\\Component\\DependencyInjection\\ParameterBag\\ParameterBag')
            ->disableOriginalConstructor()->getMock();

        $parameterBag->expects($this->any())->method('add');

        $container->expects($this->any())->method('getParameterBag')->will($this->returnValue($parameterBag));

        $extension = new BeelabTagExtension();
        $configs = [
            ['tag_class' => 'foo'],
            ['purge' => false],
        ];
        $extension->load($configs, $container);
        $this->assertTrue(true);
    }
}
