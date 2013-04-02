<?php namespace Orchestra\Facile\Tests;

class TemplateDriverTest extends \PHPUnit_Framework_TestCase {

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

		$this->assertEquals(array('html'), $formats->getValue($stub));
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
}

class TemplateDriverStub extends \Orchestra\Facile\TemplateDriver {}