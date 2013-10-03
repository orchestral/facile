<?php namespace Orchestra\Facile\Tests\Template;

use Mockery as m;
use Illuminate\Pagination\Paginator;

class DriverTest extends \PHPUnit_Framework_TestCase {

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
	 * Test construct an instance of Orchestra\Facile\Template\Driver
	 *
	 * @test
	 */
	public function testConstructMethod()
	{
		$stub = new TemplateDriverStub;

		$refl          = new \ReflectionObject($stub);
		$formats       = $refl->getProperty('formats');
		$defaultFormat = $refl->getProperty('defaultFormat');

		$formats->setAccessible(true);
		$defaultFormat->setAccessible(true);

		$this->assertEquals(array('html', 'json', 'foo'), $formats->getValue($stub));
		$this->assertEquals('html', $defaultFormat->getValue($stub));
	}

	/**
	 * Test Orchestra\Facile\Template\Driver::format() method
	 *
	 * @test
	 */
	public function testFormatMethod()
	{
		$stub = new TemplateDriverStub;

		$request = m::mock('Request');
		$request->shouldReceive('format')->once()->andReturn('html');
		\Illuminate\Support\Facades\Request::setFacadeApplication(array('request' => $request));

		$this->assertEquals('html', $stub->format());
		
		$request->shouldReceive('format')->once()->andReturn('json');
		\Illuminate\Support\Facades\Request::setFacadeApplication(array('request' => $request));

		$this->assertEquals('json', $stub->format());
	}

	/**
	 * Test Orchestra\Facile\Template\Driver::compose() method.
	 *
	 * @test
	 */
	public function testComposeMethod()
	{
		$stub = new TemplateDriverStub;
		$data = array(
			'view'   => null,
			'data'   => array(),
			'status' => 200,
		);

		$this->assertEquals('foo', $stub->compose('foo', $data));
	}

	/**
	 * Test Orchestra\Facile\Template\Driver::compose() method return response with 
	 * error 406 when given an invalid format.
	 *
	 * @test
	 */
	public function testComposeMethodReturnResponseError406WhenGivenInvalidFormat()
	{
		$stub = new TemplateDriverStub;
		$data = array(
			'view'   => null,
			'data'   => array(),
			'status' => 200,
		);

		$view = m::mock('View');
		$view->shouldReceive('exists')->once()->andReturn(false);

		\Illuminate\Support\Facades\View::swap($view);

		$response = $stub->compose('foobar', $data);
		$this->assertInstanceOf("\Illuminate\Http\Response", $response);
	}

	/**
	 * Test Orchestra\Facile\Template\Driver::compose() method throws exception 
	 * when given method isn't available.
	 *
	 * @expectedException \RuntimeException
	 */
	public function testComposeMethodThrowsExceptionWhenMethodNotAvailable()
	{
		$stub = new TemplateDriverStub;
		$data = array(
			'view'   => null,
			'data'   => array(),
			'status' => 200,
		);

		$stub->compose('json', $data);
	}

	/**
	 * Test Orchestra\Facile\Template\Driver::transform() method when item has 
	 * toArray().
	 *
	 * @test
	 */
	public function testTransformMethodWhenItemHasToArray()
	{
		$mock = m::mock('\Illuminate\Support\Contracts\ArrayableInterface');
		$mock->shouldReceive('toArray')->once()->andReturn('foobar');

		$stub = new TemplateDriverStub;
		$this->assertEquals('foobar', $stub->transform($mock));
	}

	/**
	 * Test Orchestra\Facile\Template\Driver::transform() method when item is 
	 * instance of Illuminate\Database\Eloquent\Model.
	 *
	 * @test
	 */
	public function testTransformMethodWhenItemIsInstanceOfEloquent()
	{
		$mock = m::mock('\Illuminate\Database\Eloquent\Model');
		$mock->shouldReceive('toArray')->once()->andReturn('foobar');

		$stub = new TemplateDriverStub;
		$this->assertEquals('foobar', $stub->transform($mock));
	}

	/**
	 * Test Orchestra\Facile\Template\Driver::transform() method when item is an 
	 * array.
	 *
	 * @test
	 */
	public function testTransformMethodWhenItemIsArray()
	{
		$mock = m::mock('\Illuminate\Support\Contracts\ArrayableInterface');
		$mock->shouldReceive('toArray')->once()->andReturn('foobar');

		$stub = new TemplateDriverStub;
		$this->assertEquals(array('foobar'), $stub->transform(array($mock)));
	}

	/**
	 * Test Orchestra\Facile\Template\Driver::transform() method when item has 
	 * renderable.
	 *
	 * @test
	 */
	public function testTransformMethodWhenItemIsRenderable()
	{
		$mock = m::mock('\Illuminate\Support\Contracts\RenderableInterface');
		$mock->shouldReceive('render')
				->once()
				->andReturn('<foobar>');

		$stub = new TemplateDriverStub;
		$this->assertEquals('&lt;foobar&gt;', $stub->transform($mock));
	}

	/**
	 * Test Orchestra\Facile\Template\Driver::transform() method when item 
	 * is instance of Paginator
	 *
	 * @test
	 */
	public function testTransformMethodWhenItemInstanceOfPaginator()
	{
		$results = array('foo' => 'foobar');
		$env     = m::mock('\Illuminate\Pagination\Environment');

		$env->shouldReceive('getCurrentPage')->once()->andReturn(1);

		$paginator = new Paginator($env, $results, 3, 1);
		$paginator->setupPaginationContext();


		$stub = new TemplateDriverStub;

		$expected = array(
			'total'        => 3, 
			'per_page'     => 1, 
			'current_page' => 1, 
			'last_page'    => 3,
			'from'         => 1, 
			'to'           => 1, 
			'data'         => $results,
		);

		$this->assertEquals($expected, $stub->transform($paginator));
	}
}

class TemplateDriverStub extends \Orchestra\Facile\Template\Driver {

	protected $formats = array('html', 'json', 'foo');

	public function composeFoo()
	{
		return 'foo';
	}

}
