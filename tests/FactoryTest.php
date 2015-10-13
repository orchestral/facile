<?php namespace Orchestra\Facile\TestCase;

use Mockery as m;
use Orchestra\Facile\Factory;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Teardown the test environment.
     */
    public function tearDown()
    {
        m::close();
    }

    /**
     * Test construct an instance of Orchestra\Facile\Factory.
     *
     * @test
     */
    public function testConstructMethod()
    {
        $app = m::mock('\Illuminate\Container\Container, \Illuminate\Contracts\Foundation\Application');
        $request = m::mock('\Illuminate\Http\Request');

        $stub = new Factory($app, $request);

        $refl = new \ReflectionObject($stub);
        $names = $refl->getProperty('names');
        $names->setAccessible(true);

        $this->assertTrue(is_array($names->getValue($stub)));
    }

    /**
     * Test Orchestra\Facile\Factory::make() method.
     *
     * @test
     */
    public function testMakeMethod()
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

    /**
     * Test Orchestra\Facile\Factory::view() method.
     *
     * @test
     */
    public function testViewMethod()
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

    /**
     * Test Orchestra\Facile\Factory::with() method.
     *
     * @test
     */
    public function testWithMethod()
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

    /**
     * Test Orchestra\Facile\Factory::name() method.
     *
     * @test
     */
    public function testNameMethod()
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
     * Test Orchestra\Facile\Factory::getTemplate() method throws exception
     * when template is not set.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testGetTemplateMethodThrowsExceptionWhenTempalteIsNotSet()
    {
        $app = m::spy('\Illuminate\Container\Container, \Illuminate\Contracts\Foundation\Application');
        $request = m::mock('\Illuminate\Http\Request');

        $stub = new Factory($app, $request);

        $stub->getTemplate('badFoo');
    }
}
