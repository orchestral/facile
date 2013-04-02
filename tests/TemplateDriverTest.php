<?php namespace Orchestra\Facile\Tests;

class TemplateDriverTest extends \PHPUnit_Framework_TestCase {

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
	 * Test construct an instance of Orchestra\Facile\TemplateDriver
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
	 * Test Orchestra\Facile\TemplateDriver::format() method
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
	 * Test Orchestra\Facile\Driver::compose() method.
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
	 * Test Orchestra\Facile\Driver::compose() method return response with 
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
	 * Test Orchestra\Facile\Driver::compose() method throws exception 
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
}

class TemplateDriverStub extends \Orchestra\Facile\TemplateDriver {

	protected $formats = array('html', 'json', 'foo');

	public function composeFoo($data)
	{
		return 'foo';
	}

}