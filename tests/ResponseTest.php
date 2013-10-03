<?php namespace Orchestra\Facile\Tests;

use Mockery as m;
use Illuminate\Container\Container;
use Orchestra\Facile\Response;
use Orchestra\Facile\Environment;
use Orchestra\Facile\Template\Base;

class ResponseTest extends \PHPUnit_Framework_TestCase {
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
	 * Test construct an instance of Orchestra\Facile\Response.
	 *
	 * @test
	 */
	public function testConstructMethod()
	{
		$stub = new Response(
			new Environment($this->app), 
			new Base($this->app),
			array(),
			'json'
		);
		
		$refl = new \ReflectionObject($stub);
		$data = $refl->getProperty('data');
		$data->setAccessible(true);

		$expected = array('view' => null, 'data' => array(), 'status' => 200);

		$this->assertEquals($expected, $data->getValue($stub));
	}

	/**
	 * Test Orchestra\Facile\Response::view() method.
	 *
	 * @test
	 */
	public function testViewMethod()
	{
		$stub = new Response(
			new Environment($this->app), 
			new Base($this->app),
			array(),
			'json'
		);

		$stub->view('foo.bar');

		$refl = new \ReflectionObject($stub);
		$data = $refl->getProperty('data');
		$data->setAccessible(true);

		$result = $data->getValue($stub);

		$this->assertEquals('foo.bar', $result['view']);
	}

	/**
	 * Test Orchestra\Facile\Response::with() method.
	 *
	 * @test
	 */
	public function testWithMethod()
	{
		$stub = new Response(
			new Environment($this->app), 
			new Base($this->app),
			array(),
			'json'
		);

		$stub->with('foo', 'bar');
		$stub->with(array('foobar' => 'foo'));

		$refl = new \ReflectionObject($stub);
		$data = $refl->getProperty('data');
		$data->setAccessible(true);

		$result = $data->getValue($stub);

		$this->assertEquals(array('foo' => 'bar', 'foobar' => 'foo'), $result['data']);
	}

	/**
	 * Test Orchestra\Facile\Response::status() method.
	 *
	 * @test
	 */
	public function testStatusMethod()
	{
		$stub = new Response(
			new Environment($this->app), 
			new Base($this->app),
			array(),
			'json'
		);

		$stub->status(500);

		$refl = new \ReflectionObject($stub);
		$data = $refl->getProperty('data');
		$data->setAccessible(true);

		$result = $data->getValue($stub);

		$this->assertEquals(500, $result['status']);
	}

	/**
	 * Test Orchestra\Facile\Response::template() method.
	 *
	 * @test
	 */
	public function testTemplateMethod()
	{
		$env      = new Environment($this->app);
		$template = new Base($this->app);

		$env->template('foo', $template);

		$stub = new Response($env, $template, array(), 'json');

		$stub->template('foo');

		$refl     = new \ReflectionObject($stub);
		$template = $refl->getProperty('template');
		$template->setAccessible(true);

		$this->assertInstanceOf('\Orchestra\Facile\Template\Base', $template->getValue($stub));

		$stub->template(new Base($this->app));

		$refl     = new \ReflectionObject($stub);
		$template = $refl->getProperty('template');
		$template->setAccessible(true);

		$this->assertInstanceOf('\Orchestra\Facile\Template\Base', $template->getValue($stub));
	}

	/**
	 * Test Orchestra\Facile\Response::format() method.
	 *
	 * @test
	 */
	public function testFormatMethod()
	{
		$template = m::mock('\Orchestra\Facile\Template\Base');
		$template->shouldReceive('format')->once()->andReturn('jsonp');

		$stub = new Response(new Environment($this->app), $template, array(), null);

		$this->assertEquals('jsonp', $stub->format()->format);

		$stub->format('md');

		$refl   = new \ReflectionObject($stub);
		$format = $refl->getProperty('format');
		$format->setAccessible(true);

		$this->assertEquals('md', $format->getValue($stub));
	}

	/**
	 * Test Orchestra\Facile\Response::__get() method with invalid arguments.
	 *
	 * @expectedException \InvalidArgumentException
	 */
	public function testGetMethodWithInvalidArgument()
	{
		$data = array(
			'view' => 'foo.bar',
			'data' => array('foo' => 'foo is awesome'),
			'status' => 404,
		);

		$stub = new Response(
			new Environment($this->app), 
			new Base($this->app), 
			$data, 
			'json'
		);

		$data = $stub->data;
	}

	/**
	 * Test Orchestra\Facile\Response::__toString() method.
	 *
	 * @test
	 */
	public function testToStringMethod()
	{
		$template1 = m::mock('\Orchestra\Facile\Template\Base');
		$template1->shouldReceive('setContainer')->once()->with($this->app)->andReturn(null)
			->shouldReceive('compose')->once()
				->with('json', m::any())->andReturn(json_encode(array('foo' => 'foo is awesome')));

		$stub1 = new Response(new Environment($this->app), $template1, array(), 'json');

		ob_start();
		echo $stub1;
		$output1 = ob_get_contents();
		ob_end_clean();

		$this->assertEquals('{"foo":"foo is awesome"}', $output1);

		$render = m::mock('\Illuminate\Support\Contracts\RenderableInterface');
		$render->shouldReceive('render')->once()->andReturn('foo is awesome');

		$template2 = m::mock('\Orchestra\Facile\Template\Driver');
		$template2->shouldReceive('setContainer')->once()->with($this->app)->andReturn(null)
			->shouldReceive('compose')->once()->with('json', m::any())->andReturn($render);

		$stub2 = new Response(new Environment($this->app), $template2, array(), 'json');

		ob_start();
		echo $stub2;
		$output2 = ob_get_contents();
		ob_end_clean();

		$this->assertEquals('foo is awesome', $output2);
	}
}
