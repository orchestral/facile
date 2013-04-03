<?php namespace Orchestra\Facile\Tests\Template;

class DriverTest extends \PHPUnit_Framework_TestCase {

	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		$appMock = \Mockery::mock('Application')
			->shouldReceive('instance')->andReturn(true);

		\Illuminate\Support\Facades\View::setFacadeApplication($appMock->getMock());
	}

	/**
	 * Teardown the test environment.
	 */
	public function tearDown()
	{
		\Mockery::close();
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

		$inputMock = \Mockery::mock('Request')
			->shouldReceive('input')
				->once()
				->andReturn('html');

		\Illuminate\Support\Facades\Input::setFacadeApplication(array(
			'request' => $inputMock->getMock(),
		));

		$this->assertEquals('html', $stub->format());
		
		$inputMock = \Mockery::mock('Request')
			->shouldReceive('input')
				->once()
				->andReturn('json');

		\Illuminate\Support\Facades\Input::setFacadeApplication(array(
			'request' => $inputMock->getMock(),
		));

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

		$viewMock = \Mockery::mock('View')
			->shouldReceive('exists')
				->once()
				->andReturn(false);

		\Illuminate\Support\Facades\View::swap($viewMock->getMock());

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
		$mock = \Mockery::mock('\Illuminate\Support\Contracts\ArrayableInterface')
			->shouldReceive('toArray')
				->once()
				->andReturn('foobar');

		$stub   = new TemplateDriverStub;
		$this->assertEquals('foobar', $stub->transform($mock->getMock()));
	}

	/**
	 * Test Orchestra\Facile\Template\Driver::transform() method when item is 
	 * instance of Illuminate\Database\Eloquent\Model.
	 *
	 * @test
	 */
	public function testTransformMethodWhenItemIsInstanceOfEloquent()
	{
		$mock = \Mockery::mock('\Illuminate\Database\Eloquent\Model')
				->shouldReceive('toArray')
					->once()
					->andReturn('foobar');

		$stub = new TemplateDriverStub;
		$this->assertEquals('foobar', $stub->transform($mock->getMock()));
	}

	/**
	 * Test Orchestra\Facile\Template\Driver::transform() method when item is an 
	 * array.
	 *
	 * @test
	 */
	public function testTransformMethodWhenItemIsArray()
	{
		$mock = \Mockery::mock('\Illuminate\Support\Contracts\ArrayableInterface')
			->shouldReceive('toArray')
				->once()
				->andReturn('foobar');

		$stub = new TemplateDriverStub;
		$this->assertEquals(array('foobar'), $stub->transform(array($mock->getMock())));
	}

	/**
	 * Test Orchestra\Facile\Template\Driver::transform() method when item has 
	 * renderable.
	 *
	 * @test
	 */
	public function testTransformMethodWhenItemIsRenderable()
	{
		$mock = \Mockery::mock('\Illuminate\Support\Contracts\RenderableInterface')
			->shouldReceive('render')
				->once()
				->andReturn('<foobar>');

		$stub = new TemplateDriverStub;
		$this->assertEquals('&lt;foobar&gt;', $stub->transform($mock->getMock()));
	}

	/**
	 * Test Orchestra\Facile\Template\Driver::transform() method when item 
	 * is instance of Paginator
	 *
	 * @test
	 */
	public function testTransformMethodWhenItemInstanceOfPaginator()
	{
		$mock = \Mockery::mock('Illuminate\Pagination\Paginator', array('results', array('foo' => 'foobar')))
			->shouldReceive('getItems')
				->andReturn(array('foo' => 'foobar'))
			->shouldReceive('links')
				->once()
				->andReturn('<foo>');

		$stub = new TemplateDriverStub;

		$this->assertEquals(array('results' => array('foo' => 'foobar'), 'links' => '&lt;foo&gt;'), 
			$stub->transform($mock->getMock()));
	}
}

class TemplateDriverStub extends \Orchestra\Facile\Template\Driver {

	protected $formats = array('html', 'json', 'foo');

	public function composeFoo($data)
	{
		return 'foo';
	}

}