<?php namespace Orchestra\Facile\Tests\Template;

use Mockery as m;
use Orchestra\Facile\Template\Base;

class BaseTest extends \PHPUnit_Framework_TestCase {
	
	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		$app = m::mock('Application');
		$app->shouldReceive('instance')->andReturn(true);

		\Illuminate\Support\Facades\View::setFacadeApplication($app);
	}

	/**
	 * Teardown the test environment.
	 */
	public function tearDown()
	{
		m::close();
	}

	/**
	 * Test constructing a new Orchestra\Facile\Template.
	 *
	 * @test
	 */
	public function testConstructMethod()
	{
		$stub = new Base;

		$refl          = new \ReflectionObject($stub);
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
		$data = array('foo' => 'foo is awesome');

		$view = m::mock('View');
		$view->shouldReceive('make')->with('users.index')->once()->andReturn($view)
			->shouldReceive('with')->with($data)->andReturn('foo');

		\Illuminate\Support\Facades\View::swap($view);
		$stub = with(new Base)->composeHtml('users.index', $data);

		$this->assertInstanceOf('\Illuminate\Http\Response', $stub);
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
		$stub = with(new Base)->composeHtml(null, $data);
	}

	/**
	 * Test Orchestra\Facile\Template::compose_json() method.
	 *
	 * @test
	 */
	public function testComposeJsonMethod()
	{
		$data = array('foo' => 'foobar is awesome');
		$stub = with(new Base)->composeJson(null, $data);

		$this->assertInstanceOf('\Illuminate\Http\Response', $stub);
		$this->assertEquals('{"foo":"foobar is awesome"}', $stub->getContent());
		$this->assertEquals('application/json', $stub->headers->get('content-type'));
	}
}
