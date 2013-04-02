<?php namespace Orchestra\Facile\Tests;

class ResponseTest extends \PHPUnit_Framework_TestCase {

	/**
	 * Teardown the test environment.
	 */
	public function tearDown()
	{
		\Mockery::close();
	}

	/**
	 * Test construct an instance of Orchestra\Facile\Response.
	 *
	 * @test
	 */
	public function testConstructMethod()
	{
		$stub = new \Orchestra\Facile\Response(
			new \Orchestra\Facile\Template,
			array(),
			'json'
		);

		$refl = new \ReflectionObject($stub);
		$data = $refl->getProperty('data');
		$data->setAccessible(true);

		$this->assertEquals(array('view' => null, 'data' => array(), 'status' => 200),
			$data->getValue($stub));
	}

	/**
	 * Test Orchestra\Facile\Response::view() method.
	 *
	 * @test
	 */
	public function testViewMethod()
	{
		$stub = new \Orchestra\Facile\Response(
			new \Orchestra\Facile\Template,
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
		$stub = new \Orchestra\Facile\Response(
			new \Orchestra\Facile\Template,
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
		$stub = new \Orchestra\Facile\Response(
			new \Orchestra\Facile\Template,
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
	 * Test Orchestra\Facile\Response::format() method.
	 *
	 * @test
	 */
	public function testFormatMethod()
	{
		$mock = \Mockery::mock('\Orchestra\Facile\Template')
					->shouldReceive('format')
						->once()
						->andReturn('jsonp');

		$stub = new \Orchestra\Facile\Response(
			$mock->getMock(),
			array(),
			null
		);

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
	 * @group facile
	 * @expectedException \InvalidArgumentException
	 */
	public function testGetMethodWithInvalidArgument()
	{
		$stub = new \Orchestra\Facile\Response(
			new \Orchestra\Facile\Template,
			array(
				'view' => 'foo.bar',
				'data' => array('foo' => 'foo is awesome'),
				'status' => 404,
			),
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
		$mock1 = \Mockery::mock('\Orchestra\Facile\Template')
					->shouldReceive('compose')
						->with('json', \Mockery::any())
						->once()
						->andReturn(json_encode(array('foo' => 'foo is awesome')));

		$stub1 = new \Orchestra\Facile\Response(
			$mock1->getMock(),
			array(),
			'json'
		);

		ob_start();
		echo $stub1;
		$output1 = ob_get_contents();
		ob_end_clean();

		$this->assertEquals('{"foo":"foo is awesome"}', $output1);

		$renderMock = \Mockery::mock('\Illuminate\Support\Contracts\RenderableInterface')
					->shouldReceive('render')
						->once()
						->andReturn('foo is awesome');

		$mock2 = \Mockery::mock('\Orchestra\Facile\Template')
					->shouldReceive('compose')
						->with('json', \Mockery::any())
						->once()
						->andReturn($renderMock->getMock());

		$stub2 = new \Orchestra\Facile\Response(
			$mock2->getMock(),
			array(),
			'json'
		);

		ob_start();
		echo $stub2;
		$output2 = ob_get_contents();
		ob_end_clean();

		$this->assertEquals('foo is awesome', $output2);
	}
}