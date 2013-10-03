<?php namespace Orchestra\Facile\Tests;

use Mockery as m;
use Illuminate\Container\Container;
use Orchestra\Facile\Environment;

class EnvironmentTest extends \PHPUnit_Framework_TestCase {

	/**
	 * Application instance.
	 *
	 * @var \Illuminate\Foundation\Application
	 */
	protected $app;

	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		$this->app = new Container;
	}
	/**
	 * Teardown the test environment.
	 */
	public function tearDown()
	{
		unset($this->app);
		m::close();
	}
	
	/**
	 * Test construct an instance of Orchestra\Facile\Environment.
	 *
	 * @test
	 */
	public function testConstructMethod()
	{
		$stub      = new Environment($this->app);
		$refl      = new \ReflectionObject($stub);
		$templates = $refl->getProperty('templates');
		$templates->setAccessible(true);

		$this->assertTrue(is_array($templates->getValue($stub)));
		$this->assertInstanceOf('\Illuminate\Container\Container', $stub->getContainer());
	}

	/**
	 * Test Orchestra\Facile\Environment::make() method.
	 *
	 * @test
	 */
	public function testMakeMethod()
	{
		$template = m::mock('\Orchestra\Facile\Template\Driver');
		$template->shouldReceive('compose')->with('json', m::any())->once()->andReturn('foo');

		$stub = new Environment($this->app);
		$stub->template('mock', function () use ($template)
		{
			return $template;
		});

		$response = $stub->make('mock', array('data' => array('foo' => 'foo is awesome')), 'json');

		$refl = new \ReflectionObject($response);
		$data = $refl->getProperty('data');
		$data->setAccessible(true);

		$expected = array(
			'view' => null,
			'data' => array('foo' => 'foo is awesome'),
			'status' => 200,
		);

		$this->assertInstanceOf('\Orchestra\Facile\Response', $response);
		$this->assertEquals($expected, $data->getValue($response));
		$this->assertEquals('foo', $response->render());
	}

	/**
	 * Test Orchestra\Facile\Environment::make() throws exception when using 
	 * an invalid template.
	 *
	 * @expectedException \InvalidArgumentException
	 */
	public function testMakeMethodThrowsExceptionUsingInvalidTemplate()
	{
		$stub = new Environment($this->app);

		$stub->make('foobar', array('view' => 'error.404'), 'html');
	}

	/**
	 * Test Orchestra\Facile\Environment::view() method.
	 *
	 * @test
	 */
	public function testViewMethod()
	{
		$template = m::mock('\Orchestra\Facile\Template\Driver');
		$template->shouldReceive('format')->with()->once()->andReturn('html')
			->shouldReceive('compose')->with('html', m::any())->once()->andReturn('foo');

		$stub = new Environment($this->app);
		$stub->template('default', function () use ($template)
		{
			return $template;
		});

		$response = $stub->view('foo.bar', array('foo' => 'foo is awesome'));

		$refl = new \ReflectionObject($response);
		$data = $refl->getProperty('data');
		$data->setAccessible(true);

		$expected = array(
			'view'   => 'foo.bar',
			'data'   => array('foo' => 'foo is awesome'),
			'status' => 200,
		);

		$this->assertInstanceOf('\Orchestra\Facile\Response', $response);
		$this->assertEquals($expected, $data->getValue($response));
		$this->assertEquals('foo', $response->render());
	}

	/**
	 * Test Orchestra\Facile\Environment::with() method.
	 *
	 * @test
	 */
	public function testWithMethod()
	{
		$template = m::mock('\Orchestra\Facile\Template\Driver');
		$template->shouldReceive('format')->with()->once()->andReturn('html')
			->shouldReceive('compose')->with('html', m::any())->once()->andReturn('foo');

		$stub = new Environment($this->app);
		$stub->template('default', function () use ($template)
		{
			return $template;
		});

		$response = $stub->with(array('foo' => 'foo is awesome'));

		$refl = new \ReflectionObject($response);
		$data = $refl->getProperty('data');
		$data->setAccessible(true);

		$expected = array(
			'view' => null,
			'data' => array('foo' => 'foo is awesome'),
			'status' => 200,
		);

		$this->assertInstanceOf('\Orchestra\Facile\Response', $response);
		$this->assertEquals($expected, $data->getValue($response));
		$this->assertEquals('foo', $response->render());
	}

	/**
	 * Test Orchestra\Facile\Environment::template() method.
	 *
	 * @test
	 */
	public function testTemplateMethod()
	{
		$stub      = new Environment($this->app);
		$refl      = new \ReflectionObject($stub);
		$templates = $refl->getProperty('templates');
		$templates->setAccessible(true);

		$this->assertTrue(is_array($templates->getValue($stub)));

		$stub->template('foo', new FooTemplateStub);
	}

	/**
	 * Test Orchestra\Facile\Environment::template() method throws exception 
	 * when template is not instanceof \Orchestra\Facile\Template\Driver
	 *
	 * @test
	 * @expectedException RuntimeException
	 */
	public function testTemplateMethodThrowsException()
	{
		$stub = new Environment($this->app);
		$stub->template('badFoo', new BadFooTemplateStub);
	}
}

class FooTemplateStub extends \Orchestra\Facile\Template\Driver {}

class BadFooTemplateStub {}
