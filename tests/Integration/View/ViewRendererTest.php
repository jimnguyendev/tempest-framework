<?php

declare(strict_types=1);

namespace Tests\Tempest\Integration\View;

use function Tempest\view;
use Tests\Tempest\Integration\FrameworkIntegrationTestCase;

/**
 * @internal
 * @small
 */
class ViewRendererTest extends FrameworkIntegrationTestCase
{
    public function test_view_renderer(): void
    {
        $this->assertSame(
            '<h1>Hello</h1>',
            $this->render('<h1>Hello</h1>'),
        );

        $this->assertSame(
            '<h1>Hello</h1>',
            $this->render(view('<h1>{{ $this->foo }}</h1>')->data(foo: 'Hello')),
        );

        $this->assertSame(
            '<h1></h1>',
            $this->render(view('<h1>{{ $this->foo }}</h1>')),
        );

        $this->assertSame(
            '<h1>Hello</h1>',
            $this->render(view('<h1>{{ $this->raw("foo") }}</h1>')->data(foo: 'Hello')),
        );
    }

    public function test_if_attribute()
    {
        $this->assertSame(
            '',
            $this->render(view('<div :if="$this->show">Hello</div>')->data(show: false)),
        );

        $this->assertSame(
            '<div :if="$this->show">Hello</div>',
            $this->render(view('<div :if="$this->show">Hello</div>')->data(show: true)),
        );
    }

    public function test_else_attribute(): void
    {
        $this->assertSame(
            '<div :if="$this->show">True</div>',
            $this->render(view('<div :if="$this->show">True</div><div :else>False</div>')->data(show: true)),
        );

        $this->assertSame(
            '<div :else>False</div>',
            $this->render(view('<div :if="$this->show">True</div><div :else>False</div>')->data(show: false)),
        );
    }

    public function test_foreach_attribute(): void
    {
        $this->assertSame(
            <<<'HTML'
            <div :foreach="$this->items as $foo">a</div>
            <div :foreach="$this->items as $foo">b</div>
            HTML,
            $this->render(view('<div :foreach="$this->items as $foo">{{ $foo }}</div>')->data(items: ['a', 'b'])),
        );
    }

    public function test_forelse_attribute(): void
    {
        $this->assertSame(
            <<<'HTML'
            <div :forelse>Empty</div>
            HTML,
            $this->render(view('<div :foreach="$this->items as $foo">{{ $foo }}</div><div :forelse>Empty</div>')->data(items: [])),
        );

        $this->assertSame(
            <<<'HTML'
            <div :foreach="$this->items as $foo">a</div>
            HTML,
            $this->render(view('<div :foreach="$this->items as $foo">{{ $foo }}</div><div :forelse>Empty</div>')->data(items: ['a'])),
        );
    }

    public function test_default_slot(): void
    {
        $this->assertSame(
            <<<'HTML'
            <div class="base">
                
                    Test
                
            </div>
            HTML,
            $this->render(
                <<<'HTML'
                <x-base-layout>
                    <x-slot>
                        Test
                    </x-slot>
                </x-base-layout>
                HTML,
            ),
        );
    }

    public function test_implicit_default_slot(): void
    {
        $this->assertSame(
            <<<'HTML'
            <div class="base">
                
                Test
            
            </div>
            HTML,
            $this->render(
                <<<'HTML'
                <x-base-layout>
                    Test
                </x-base-layout>
                HTML,
            ),
        );
    }

    public function test_multiple_slots(): void
    {
        $this->assertSame(
            <<<'HTML'
            injected scripts
                
            
            
            <div class="base">
                
                Test
                
                
            
                
                Hi
            
            </div>
            
            
            
                injected styles
            HTML,
            $this->render(
                <<<'HTML'
            <x-complex-base>
                Test
                
                <x-slot name="scripts">
                injected scripts
                </x-slot>
                
                <x-slot name="styles">
                injected styles
                </x-slot>
                
                Hi
            </x-complex-base>
            HTML,
            ),
        );
    }

    public function test_pre(): void
    {
        $this->assertSame(
            <<<'HTML'
            <pre>
            a
                    <span class="hl-prop">b</span>
               <span class="hl-type">c</span>
            </pre>
            HTML,
            $this->render(
                <<<'HTML'
            <pre>
            a
                    <span class="hl-prop">b</span>
               <span class="hl-type">c</span>
            </pre>
            HTML,
            ),
        );
    }
}
