<?php

namespace Orchestra\Facile\TestCase\Feature;

use Mockery as m;
use Orchestra\Facile\Template\Simple;
use Orchestra\Support\Facades\Facile;

class FactoryTest extends TestCase
{

    /** @test */
    public function it_can_construct_facile_using_name()
    {
        $template = m::mock('FooTemplateStub', '\Orchestra\Facile\Template\Template');

        Facile::name('foo', $template);

        $this->assertSame($template, Facile::getTemplate('foo'));
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function it_throws_exception_when_trying_to_get_template_without_defining_it_first()
    {
        $this->withoutExceptionHandling();

        Facile::getTemplate('badFoo');
    }
}
