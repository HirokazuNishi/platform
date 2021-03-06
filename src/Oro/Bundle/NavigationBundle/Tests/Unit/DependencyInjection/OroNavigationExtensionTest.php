<?php

namespace Oro\Bundle\NavigationBundle\Tests\Unit\DependencyInjection;

use Oro\Bundle\NavigationBundle\DependencyInjection\OroNavigationExtension;

class OroNavigationExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function testLoad()
    {
        $extension = new OroNavigationExtension();

        $configs = array();
        $container = $this->getMock('Symfony\Component\DependencyInjection\ContainerBuilder');

        $container->expects($this->any())
            ->method('getParameter')
            ->with('kernel.bundles')
            ->will($this->returnValue(array('Oro\Bundle\NavigationBundle\OroNavigationBundle')));
        $isCalled = false;
        $container->expects($this->any())
            ->method('setParameter')
            ->will(
                $this->returnCallback(
                    function ($name, $value) use (&$isCalled) {
                        if ($name == 'oro_menu_config' && is_array($value)) {
                            $isCalled = true;
                        }
                    }
                )
            );
        $extension->load($configs, $container);
        $this->assertTrue($isCalled);
    }

    public function testGetAssets()
    {
        $extension = new OroNavigationExtension();

        $container = $this->getMock('Symfony\Component\DependencyInjection\ContainerBuilder');

        $container->expects($this->once())
            ->method('getParameter')
            ->will(
                $this->returnValue(
                    array(
                        'Oro\Bundle\NavigationBundle\Tests\Unit\Fixtures\TestBundle'
                    )
                )
            );

        $assets = $extension->getAssets($container);

        $this->assertEquals('second.css', $assets['css'][1]);
        $this->assertEquals('first.js', $assets['js'][0]);
    }
}
