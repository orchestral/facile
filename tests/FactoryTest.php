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
        $request = m::mock('\Illuminate\Http\Request');

        $stub = new Factory($request);

        $refl      = new \ReflectionObject($stub);
        $templates = $refl->getProperty('templates');
        $templates->setAccessible(true);

        $this->assertTrue(is_array($templates->getValue($stub)));
    }

    /**
     * Test Orchestra\Facile\Factory::make() method.
     *
     * @test
     */
    public function testMakeMethod()
    {
        $request  = m::mock('\Illuminate\Http\Request');
        $template = m::mock('\Orchestra\Facile\Template\Template');

        $template->shouldReceive('compose')->once()->with('json', m::any())->andReturn('foo');

        $stub = new Factory($request);

        $stub->template('mock', function () use ($template) {
            return $template;
        });

        $container = $stub->make('mock', ['data' => ['foo' => 'foo is awesome']], 'json');

        $refl = new \ReflectionObject($container);
        $data = $refl->getProperty('data');
        $data->setAccessible(true);

        $expected = [
            'view'   => null,
            'data'   => ['foo' => 'foo is awesome'],
            'status' => 200,
            'on'     => [
                'html' => ['only' => null, 'except' => null],
                'json' => ['only' => null, 'except' => null],
                'csv'  => ['uses' => 'data'],
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
        $request  = m::mock('\Illuminate\Http\Request');
        $template = m::mock('\Orchestra\Facile\Template\Template');

        $request->shouldReceive('format')->once()->with('html')->andReturn('html');
        $template->shouldReceive('getDefaultFormat')->once()->with()->andReturn('html')
            ->shouldReceive('compose')->once()->with('html', m::any())->andReturn('foo');

        $stub = new Factory($request);
        $stub->template('default', function () use ($template) {
            return $template;
        });

        $container = $stub->view('foo.bar', ['foo' => 'foo is awesome']);

        $refl = new \ReflectionObject($container);
        $data = $refl->getProperty('data');
        $data->setAccessible(true);

        $expected = [
            'view'   => 'foo.bar',
            'data'   => ['foo' => 'foo is awesome'],
            'status' => 200,
            'on'     => [
                'html' => ['only' => null, 'except' => null],
                'json' => ['only' => null, 'except' => null],
                'csv'  => ['uses' => 'data'],
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
        $request  = m::mock('\Illuminate\Http\Request');
        $template = m::mock('TemplateDriver', '\Orchestra\Facile\Template\Template');

        $request->shouldReceive('format')->once()->with('html')->andReturn('html');
        $template->shouldReceive('getDefaultFormat')->once()->with()->andReturn('html')
            ->shouldReceive('compose')->once()->with('html', m::any())->andReturn('foo');

        $stub = new Factory($request);

        $stub->template('default', function () use ($template) {
            return $template;
        });

        $container = $stub->with(['foo' => 'foo is awesome']);

        $refl = new \ReflectionObject($container);
        $data = $refl->getProperty('data');
        $data->setAccessible(true);

        $expected = [
            'view'   => null,
            'data'   => ['foo' => 'foo is awesome'],
            'status' => 200,
            'on'     => [
                'html' => ['only' => null, 'except' => null],
                'json' => ['only' => null, 'except' => null],
                'csv'  => ['uses' => 'data'],
            ],
        ];

        $this->assertInstanceOf('\Orchestra\Facile\Facile', $container);
        $this->assertEquals($expected, $data->getValue($container));
        $this->assertEquals('foo', $container->render());
    }

    /**
     * Test Orchestra\Facile\Factory::template() method.
     *
     * @test
     */
    public function testTemplateMethod()
    {
        $request = m::mock('\Illuminate\Http\Request');

        $stub = new Factory($request);

        $refl      = new \ReflectionObject($stub);
        $templates = $refl->getProperty('templates');
        $templates->setAccessible(true);

        $this->assertTrue(is_array($templates->getValue($stub)));

        $template = m::mock('FooTemplateStub', '\Orchestra\Facile\Template\Template');
        $stub->template('foo', $template);
    }

    /**
     * Test Orchestra\Facile\Factory::template() method throws exception
     * when template is not instanceof \Orchestra\Facile\Template\Template.
     *
     * @expectedException \RuntimeException
     */
    public function testTemplateMethodThrowsException()
    {
        $request  = m::mock('\Illuminate\Http\Request');
        $template = m::mock('BadFooTemplateStub');

        $stub = new Factory($request);

        $stub->template('badFoo', $template);
    }

    /**
     * Test Orchestra\Facile\Factory::getTemplate() method throws exception
     * when template is not set.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testGetTemplateMethodThrowsExceptionWhenTempalteIsNotSet()
    {
        $request = m::mock('\Illuminate\Http\Request');

        $stub = new Factory($request);

        $stub->getTemplate('badFoo');
    }
}
