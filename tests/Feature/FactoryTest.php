<?php

namespace Orchestra\Facile\TestCase\Feature;

use Mockery as m;
use Orchestra\Support\Facades\Facile;

class FactoryTest extends TestCase
{
    /** @test */
    public function it_can_construct_facile_using_name()
    {
        $template = m::mock('FooParser', '\Orchestra\Facile\Template\Parser');

        Facile::name('foo', $template);

        $this->assertSame($template, Facile::parse('foo'));
    }

    /** @test */
    public function it_throws_exception_when_trying_to_get_template_without_defining_it_first()
    {
        $this->expectException('InvalidArgumentException');

        $this->withoutExceptionHandling();

        Facile::parse('badFoo');
    }
}
