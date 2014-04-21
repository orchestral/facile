<?php namespace Orchestra\Facile\TestCase;

use Mockery as m;
use Orchestra\Facile\Environment;

class EnvironmentTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Teardown the test environment.
     */
    public function tearDown()
    {
        m::close();
    }

    /**
     * Test construct an instance of Orchestra\Facile\Environment.
     *
     * @test
     */
    public function testConstructMethod()
    {
        $request = m::mock('\Illuminate\Http\Request');

        $stub = new Environment($request);

        $refl      = new \ReflectionObject($stub);
        $templates = $refl->getProperty('templates');
        $templates->setAccessible(true);

        $this->assertTrue(is_array($templates->getValue($stub)));
    }

    /**
     * Test Orchestra\Facile\Environment::make() method.
     *
     * @test
     */
    public function testMakeMethod()
    {
        $request  = m::mock('\Illuminate\Http\Request');
        $template = m::mock('\Orchestra\Facile\Template\Driver');

        $template->shouldReceive('compose')->once()->with('json', m::any())->andReturn('foo');

        $stub = new Environment($request);

        $stub->template('mock', function () use ($template) {
            return $template;
        });

        $container = $stub->make('mock', array('data' => array('foo' => 'foo is awesome')), 'json');

        $refl = new \ReflectionObject($container);
        $data = $refl->getProperty('data');
        $data->setAccessible(true);

        $expected = array(
            'view' => null,
            'data' => array('foo' => 'foo is awesome'),
            'status' => 200,
            'on'     => array(
                'html' => array('only' => null, 'except' => null),
                'json' => array('only' => null, 'except' => null),
                'csv'  => array('uses' => 'data'),
            ),
        );

        $this->assertInstanceOf('\Orchestra\Facile\Container', $container);
        $this->assertEquals($expected, $data->getValue($container));
        $this->assertEquals('foo', $container->render());
    }

    /**
     * Test Orchestra\Facile\Environment::view() method.
     *
     * @test
     */
    public function testViewMethod()
    {
        $request  = m::mock('\Illuminate\Http\Request');
        $template = m::mock('\Orchestra\Facile\Template\Driver');

        $request->shouldReceive('format')->once()->with('html')->andReturn('html');
        $template->shouldReceive('getDefaultFormat')->once()->with()->andReturn('html')
            ->shouldReceive('compose')->once()->with('html', m::any())->andReturn('foo');

        $stub = new Environment($request);
        $stub->template('default', function () use ($template) {
            return $template;
        });

        $container = $stub->view('foo.bar', array('foo' => 'foo is awesome'));

        $refl = new \ReflectionObject($container);
        $data = $refl->getProperty('data');
        $data->setAccessible(true);

        $expected = array(
            'view'   => 'foo.bar',
            'data'   => array('foo' => 'foo is awesome'),
            'status' => 200,
            'on'     => array(
                'html' => array('only' => null, 'except' => null),
                'json' => array('only' => null, 'except' => null),
                'csv'  => array('uses' => 'data'),
            ),
        );

        $this->assertInstanceOf('\Orchestra\Facile\Container', $container);
        $this->assertEquals($expected, $data->getValue($container));
        $this->assertEquals('foo', $container->render());
    }

    /**
     * Test Orchestra\Facile\Environment::with() method.
     *
     * @test
     */
    public function testWithMethod()
    {
        $request  = m::mock('\Illuminate\Http\Request');
        $template = m::mock('TemplateDriver', '\Orchestra\Facile\Template\Driver');

        $request->shouldReceive('format')->once()->with('html')->andReturn('html');
        $template->shouldReceive('getDefaultFormat')->once()->with()->andReturn('html')
            ->shouldReceive('compose')->once()->with('html', m::any())->andReturn('foo');

        $stub = new Environment($request);

        $stub->template('default', function () use ($template) {
            return $template;
        });

        $container = $stub->with(array('foo' => 'foo is awesome'));

        $refl = new \ReflectionObject($container);
        $data = $refl->getProperty('data');
        $data->setAccessible(true);

        $expected = array(
            'view' => null,
            'data' => array('foo' => 'foo is awesome'),
            'status' => 200,
            'on'     => array(
                'html' => array('only' => null, 'except' => null),
                'json' => array('only' => null, 'except' => null),
                'csv'  => array('uses' => 'data'),
            ),
        );

        $this->assertInstanceOf('\Orchestra\Facile\Container', $container);
        $this->assertEquals($expected, $data->getValue($container));
        $this->assertEquals('foo', $container->render());
    }

    /**
     * Test Orchestra\Facile\Environment::template() method.
     *
     * @test
     */
    public function testTemplateMethod()
    {
        $request = m::mock('\Illuminate\Http\Request');

        $stub = new Environment($request);

        $refl      = new \ReflectionObject($stub);
        $templates = $refl->getProperty('templates');
        $templates->setAccessible(true);

        $this->assertTrue(is_array($templates->getValue($stub)));

        $template = m::mock('FooTemplateStub', '\Orchestra\Facile\Template\Driver');
        $stub->template('foo', $template);
    }

    /**
     * Test Orchestra\Facile\Environment::template() method throws exception
     * when template is not instanceof \Orchestra\Facile\Template\Driver
     *
     * @expectedException \RuntimeException
     */
    public function testTemplateMethodThrowsException()
    {
        $request  = m::mock('\Illuminate\Http\Request');
        $template = m::mock('BadFooTemplateStub');

        $stub = new Environment($request);

        $stub->template('badFoo', $template);
    }

    /**
     * Test Orchestra\Facile\Environment::getTemplate() method throws exception
     * when template is not set.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testGetTemplateMethodThrowsExceptionWhenTempalteIsNotSet()
    {
        $request = m::mock('\Illuminate\Http\Request');

        $stub = new Environment($request);

        $stub->getTemplate('badFoo');
    }
}
