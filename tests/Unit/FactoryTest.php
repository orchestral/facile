<?php

namespace Orchestra\Facile\TestCase\Unit;

use Mockery as m;
use Orchestra\Facile\Factory;
use PHPUnit\Framework\TestCase;

class FactoryTest extends TestCase
{
    /**
     * Teardown the test environment.
     */
    protected function tearDown()
    {
        m::close();
    }

    /** @test */
    public function it_can_construct_the_factory()
    {
        $app = m::mock('\Illuminate\Container\Container, \Illuminate\Contracts\Foundation\Application');
        $request = m::mock('\Illuminate\Http\Request');

        $stub = new Factory($app, $request);

        $refl = new \ReflectionObject($stub);
        $names = $refl->getProperty('names');
        $names->setAccessible(true);

        $this->assertTrue(is_array($names->getValue($stub)));
    }

    /** @test */
    public function it_can_construct_facile_using_make()
    {
        $app = m::mock('\Illuminate\Container\Container, \Illuminate\Contracts\Foundation\Application');
        $request = m::mock('\Illuminate\Http\Request');
        $template = m::mock('\Orchestra\Facile\Template\Template');

        $template->shouldReceive('compose')->once()->with('json', m::any())->andReturn('foo');

        $stub = new Factory($app, $request);

        $stub->name('mock', $template);

        $container = $stub->make('mock', ['data' => ['foo' => 'foo is awesome']], 'json');

        $refl = new \ReflectionObject($container);
        $data = $refl->getProperty('data');
        $data->setAccessible(true);

        $expected = [
            'view' => null,
            'data' => ['foo' => 'foo is awesome'],
            'status' => 200,
            'on' => [
                'csv' => ['only' => null, 'except' => null, 'uses' => 'data'],
                'html' => ['only' => null, 'except' => null],
                'json' => ['only' => null, 'except' => null],
                'xml' => ['only' => null, 'except' => null, 'root' => null],
            ],
        ];

        $this->assertInstanceOf('\Orchestra\Facile\Facile', $container);
        $this->assertEquals($expected, $data->getValue($container));
        $this->assertEquals('foo', $container->render());
    }

    /** @test */
    public function it_can_construct_facile_using_view()
    {
        $app = m::spy('\Illuminate\Container\Container, \Illuminate\Contracts\Foundation\Application');
        $request = m::mock('\Illuminate\Http\Request');
        $template = m::mock('\Orchestra\Facile\Template\Template');

        $request->shouldReceive('prefers')->once()->with('html')->andReturn('html');
        $template->shouldReceive('getSupportedFormats')->once()->with()->andReturn('html')
            ->shouldReceive('compose')->once()->with('html', m::any())->andReturn('foo');

        $stub = new Factory($app, $request);
        $stub->name('simple', $template);

        $container = $stub->view('foo.bar', ['foo' => 'foo is awesome']);

        $refl = new \ReflectionObject($container);
        $data = $refl->getProperty('data');
        $data->setAccessible(true);

        $expected = [
            'view' => 'foo.bar',
            'data' => ['foo' => 'foo is awesome'],
            'status' => 200,
            'on' => [
                'csv' => ['only' => null, 'except' => null, 'uses' => 'data'],
                'html' => ['only' => null, 'except' => null],
                'json' => ['only' => null, 'except' => null],
                'xml' => ['only' => null, 'except' => null, 'root' => null],
            ],
        ];

        $this->assertInstanceOf('\Orchestra\Facile\Facile', $container);
        $this->assertEquals($expected, $data->getValue($container));
        $this->assertEquals('foo', $container->render());
    }

    /** @test */
    public function it_can_construct_facile_using_with()
    {
        $app = m::mock('\Illuminate\Container\Container, \Illuminate\Contracts\Foundation\Application');
        $request = m::mock('\Illuminate\Http\Request');
        $template = m::mock('TemplateDriver', '\Orchestra\Facile\Template\Template');

        $request->shouldReceive('prefers')->once()->with('html')->andReturn('html');
        $template->shouldReceive('getSupportedFormats')->once()->with()->andReturn('html')
            ->shouldReceive('compose')->once()->with('html', m::any())->andReturn('foo');

        $stub = new Factory($app, $request);

        $stub->name('simple', $template);

        $container = $stub->with(['foo' => 'foo is awesome']);

        $refl = new \ReflectionObject($container);
        $data = $refl->getProperty('data');
        $data->setAccessible(true);

        $expected = [
            'view' => null,
            'data' => ['foo' => 'foo is awesome'],
            'status' => 200,
            'on' => [
                'csv' => ['only' => null, 'except' => null, 'uses' => 'data'],
                'html' => ['only' => null, 'except' => null],
                'json' => ['only' => null, 'except' => null],
                'xml' => ['only' => null, 'except' => null, 'root' => null],
            ],
        ];

        $this->assertInstanceOf('\Orchestra\Facile\Facile', $container);
        $this->assertEquals($expected, $data->getValue($container));
        $this->assertEquals('foo', $container->render());
    }

    /** @test */
    public function it_can_construct_facile_using_name()
    {
        $app = m::mock('\Illuminate\Container\Container, \Illuminate\Contracts\Foundation\Application');
        $request = m::mock('\Illuminate\Http\Request');

        $stub = new Factory($app, $request);

        $refl = new \ReflectionObject($stub);
        $names = $refl->getProperty('names');
        $names->setAccessible(true);

        $this->assertTrue(is_array($names->getValue($stub)));

        $template = m::mock('FooTemplateStub', '\Orchestra\Facile\Template\Template');
        $stub->name('foo', $template);
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function it_throws_exception_when_trying_to_get_template_without_defining_it_first()
    {
        $app = m::spy('\Illuminate\Container\Container, \Illuminate\Contracts\Foundation\Application');
        $request = m::mock('\Illuminate\Http\Request');

        $stub = new Factory($app, $request);

        $stub->getTemplate('badFoo');
    }
}
