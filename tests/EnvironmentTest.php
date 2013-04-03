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
	 */
	public function testMakeMethod()
	{
		$templateMock = \Mockery::mock('\Orchestra\Facile\Template\Driver')
			->shouldReceive('compose')
				->with('json', \Mockery::any())
				->once()
				->andReturn('foo');

		$stub = new \Orchestra\Facile\Environment;
		$stub->template('mock', function () use ($templateMock)
		{
			return $templateMock->getMock();
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
		$stub = new \Orchestra\Facile\Environment;

		$stub->make('foobar', array('view' => 'error.404'), 'html');
	}

	/**
	 * Test Orchestra\Facile\Environment::view() method.
	 *
	 * @test
	 */
	public function testViewMethod()
	{
		$templateMock = \Mockery::mock('\Orchestra\Facile\Template\Driver')
			->shouldReceive('format')
				->with()
				->once()
				->andReturn('html')
			->shouldReceive('compose')
				->with('html', \Mockery::any())
				->once()
				->andReturn('foo');

		$stub = new \Orchestra\Facile\Environment;
		$stub->template('default', function () use ($templateMock)
		{
			return $templateMock->getMock();
		});

		$response = $stub->view('foo.bar', array('foo' => 'foo is awesome'));

		$refl = new \ReflectionObject($response);
		$data = $refl->getProperty('data');
		$data->setAccessible(true);

		$expected = array(
			'view' => 'foo.bar',
			'data' => array('foo' => 'foo is awesome'),
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
		$templateMock = \Mockery::mock('\Orchestra\Facile\Template\Driver')
			->shouldReceive('format')
				->with()
				->once()
				->andReturn('html')
			->shouldReceive('compose')
				->with('html', \Mockery::any())
				->once()
				->andReturn('foo');

		$stub = new \Orchestra\Facile\Environment;
		$stub->template('default', function () use ($templateMock)
		{
			return $templateMock->getMock();
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
		$stub = new \Orchestra\Facile\Environment;

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
		$stub = new \Orchestra\Facile\Environment;
		$stub->template('badFoo', new BadFooTemplateStub);
	}
}

class FooTemplateStub extends \Orchestra\Facile\Template\Driver {}

class BadFooTemplateStub {}