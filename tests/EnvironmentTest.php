<?php namespace Orchestra\Facile\Tests;

class EnvironmentTest extends \PHPUnit_Framework_TestCase {

	/**
	 * Teardown the test environment.
	 */
	public function tearDown()
	{
		\Mockery::close();
	}
	
	/**
	 * Test construct an instance of Orchestra\Facile\Environment.
	 *
	 * @test
	 */
	public function testConstructMethod()
	{
		$stub = new \Orchestra\Facile\Environment;

		$refl      = new \ReflectionObject($stub);
		$templates = $refl->getProperty('templates');
		$templates->setAccessible(true);

		$this->assertTrue(is_array($templates->getValue($stub)));
	}

	/**
	 * Test Orchestra\Facile\Environment::make() method.
	 *
	 * @test
	 * @group facile
	 */
	public function testMakeMethod()
	{
		$templateMock = \Mockery::mock('\Orchestra\Facile\TemplateDriver')
			->shouldReceive('compose')
				->with('json', \Mockery::any())
				->once()
				->andReturn('foo');

		$stub = new \Orchestra\Facile\Environment;
		$stub->template('mock', function () use ($templateMock)
		{
			return $templateMock->getMock();
		});

		$response = $stub->make('mock', array('data' => 'foo'), 'json');

		$this->assertInstanceOf('\Orchestra\Facile\Response', $response);
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
		$stub = new \Orchestra\Facile\Environment;

		$stub->make('foobar', array('view' => 'error.404'), 'html');
	}

	/**
	 * Test Orchestra\Facile\Environment::template() method.
	 *
	 * @test
	 */
	public function testTemplateMethod()
	{
		$stub = new \Orchestra\Facile\Environment;

		$refl      = new \ReflectionObject($stub);
		$templates = $refl->getProperty('templates');
		$templates->setAccessible(true);

		$this->assertTrue(is_array($templates->getValue($stub)));

		$stub->template('foo', new FooTemplateStub);
	}

	/**
	 * Test Orchestra\Facile\Environment::template() method throws exception 
	 * when template is not instanceof \Orchestra\Facile\TemplateDriver
	 *
	 * @test
	 * @expectedException RuntimeException
	 */
	public function testTemplateMethodThrowsException()
	{
		$stub = new \Orchestra\Facile\Environment;
		$stub->template('badFoo', new BadFooTemplateStub);
	}
}

class FooTemplateStub extends \Orchestra\Facile\TemplateDriver {}

class BadFooTemplateStub {}