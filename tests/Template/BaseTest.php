<?php namespace Orchestra\Facile\Tests\Template;

use Mockery as m;
use Illuminate\Container\Container;
use Orchestra\Facile\Template\Base;

class BaseTest extends \PHPUnit_Framework_TestCase {
	
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
	 * Test constructing a new Orchestra\Facile\Template.
	 *
	 * @test
	 */
	public function testConstructMethod()
	{
		$stub = new Base($this->app);
		$refl = new \ReflectionObject($stub);
		
		$formats       = $refl->getProperty('formats');
		$defaultFormat = $refl->getProperty('defaultFormat');

		$formats->setAccessible(true);
		$defaultFormat->setAccessible(true);

		$this->assertEquals(array('html', 'json'), $formats->getValue($stub));
		$this->assertEquals('html', $defaultFormat->getValue($stub));
	}

	/**
	 * Test Orchestra\Facile\Template::compose_html() method.
	 *
	 * @test
	 */
	public function testComposeHtmlMethod()
	{
		$app = $this->app;
		$app['view'] = $view = m::mock('View');

		$data = array('foo' => 'foo is awesome');

		$view->shouldReceive('make')->with('users.index')->once()->andReturn($view)
			->shouldReceive('with')->with($data)->andReturn('foo');

		$stub = new Base($app);

		$this->assertInstanceOf('\Illuminate\Http\Response', $stub->composeHtml('users.index', $data));
	}

	/**
	 * Test Orchestra\Facile\Template::compose_html() method throws exception
	 * when view is not defined
	 *
	 * @expectedException \InvalidArgumentException
	 */
	public function testComposeHtmlMethodThrowsException()
	{
		$data = array('foo' => 'foobar is awesome');
		
		with(new Base($this->app))->composeHtml(null, $data);
	}

	/**
	 * Test Orchestra\Facile\Template::compose_json() method.
	 *
	 * @test
	 */
	public function testComposeJsonMethod()
	{
		$data = array('foo' => 'foobar is awesome');
		$stub = with(new Base($this->app))->composeJson(null, $data);

		$this->assertInstanceOf('\Illuminate\Http\JsonResponse', $stub);
		$this->assertEquals('{"foo":"foobar is awesome"}', $stub->getContent());
		$this->assertEquals('application/json', $stub->headers->get('content-type'));
	}
}
