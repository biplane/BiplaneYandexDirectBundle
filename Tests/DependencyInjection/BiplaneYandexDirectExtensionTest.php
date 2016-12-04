<?php

namespace Biplane\YandexDirectBundle\Tests\DependencyInjection;

use Biplane\Bundle\YandexDirectBundle\DependencyInjection\BiplaneYandexDirectExtension;
use Biplane\YandexDirect\EventListener\DumpListener;
use Biplane\YandexDirect\User;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Denis Vasilev <yethee@biplane.ru>
 */
class BiplaneYandexDirectExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ContainerBuilder;
     */
    private $container;

    /**
     * @var BiplaneYandexDirectExtension;
     */
    private $extension;

    public function testLoadWithDefaults()
    {
        $this->load(array());

        $this->assertDICConstructorArguments(
            $this->container->getDefinition('biplane_yandex_direct.factory'),
            array(
                new Reference('event_dispatcher'),
                User::LOCALE_RU
            )
        );
        $this->assertDICConstructorArguments(
            $this->container->getDefinition('biplane_yandex_direct.dumper'),
            array('%kernel.cache_dir%/api_dumps')
        );
        $this->assertFalse($this->container->hasDefinition('biplane_yandex_direct.event_listener.dump'));
    }

    public function testLoadWithUserOptions()
    {
        $userOptions = array(
            'access_token' => 'foo'
        );

        $this->load(array(
            'user' => $userOptions
        ));

        $this->assertDICConstructorArguments(
            $this->container->getDefinition('biplane_yandex_direct.factory'),
            array(
                new Reference('event_dispatcher'),
                User::LOCALE_RU,
                $userOptions
            )
        );
    }

    public function testLoadWhenDumpListenerIsEnabled()
    {
        $this->load(array(
            'dump_listener' => true
        ));

        $this->assertDICConstructorArguments(
            $this->container->getDefinition('biplane_yandex_direct.event_listener.dump'),
            array(
            new Reference('biplane_yandex_direct.dumper'),
                DumpListener::LEVEL_ALL_REQUEST
            )
        );
    }

    public function testLoadWhenSandboxIsEnabled()
    {
        $this->load(array(
            'sandbox' => true,
        ));

        $this->assertDICDefinitionMethodCallAt(
            0,
            $this->container->getDefinition('biplane_yandex_direct.factory'),
            'enableSandbox'
        );
    }

    protected function setUp()
    {
        $this->container = new ContainerBuilder();
        $this->extension = new BiplaneYandexDirectExtension();
    }

    protected function tearDown()
    {
        unset($this->container, $this->extension);
    }

    private function load(array $config)
    {
        $this->extension->load(
            array(
                'biplane_yandex_direct' => $config
            ),
            $this->container
        );
    }

    private function assertDICConstructorArguments(Definition $definition, $args)
    {
        $message = sprintf(
            "Expected and actual DIC Service constructor arguments of definition '%s' don't match.",
            $definition->getClass()
        );

        $this->assertEquals($args, $definition->getArguments(), $message);
    }

    private function assertDICDefinitionMethodCallAt($pos, Definition $definition, $methodName, array $params = null)
    {
        $calls = $definition->getMethodCalls();

        if (isset($calls[$pos][0])) {
            $this->assertEquals(
                $methodName,
                $calls[$pos][0],
                sprintf('Method "%s" is expected to be called at position %d.', $methodName, $pos)
            );

            if ($params !== null) {
                $this->assertEquals(
                    $params,
                    $calls[$pos][1],
                    sprintf('Expected parameters to methods "%s" do not match the actual parameters.', $methodName)
                );
            }
        } else {
            $this->fail(sprintf('Method "%s" is expected to be called at position %d.', $methodName, $pos));
        }
    }
}
