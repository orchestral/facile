<?php namespace Orchestra\Facile\Tests;

class TemplateTest extends \PHPUnit_Framework_TestCase {
	
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
	 * Test constructing a new Orchestra\Facile\Template.
	 *
	 * @test
	 * @group facile
	 */
	public function testConstructMethod()
	{
		$stub = new \Orchestra\Facile\Template;

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
	 * @group facile
	 */
	public function testComposeHtmlMethod()
	{
		$data = array('foo' => 'foo is awesome');

		$viewMock = \Mockery::mock('View')
			->shouldReceive('make')
				->with('users.index')
				->once()
				->andReturn(\Mockery::self())
			->shouldReceive('with')
				->with($data)
				->andReturn('foo');

		\Illuminate\Support\Facades\View::swap($viewMock->getMock());
		$stub = with(new \Orchestra\Facile\Template)->composeHtml('users.index', $data);

		$this->assertInstanceOf('\Illuminate\Http\Response', $stub);
	}

	/**
	 * Test Orchestra\Facile\Template::compose_html() method throws exception
	 * when view is not defined
	 *
	 * @group facile
	 * @expectedException \InvalidArgumentException
	 */
	public function testComposeHtmlMethodThrowsException()
	{
		$data = array('foo' => 'foobar is awesome');
		$stub = with(new \Orchestra\Facile\Template)->composeHtml(null, $data);
	}

	/**
	 * Test Orchestra\Facile\Template::compose_json() method.
	 *
	 * @test
	 * @group facile
	 */
	public function testComposeJsonMethod()
	{
		$data = array('foo' => 'foobar is awesome');
		$stub = with(new \Orchestra\Facile\Template)->composeJson(null, $data);

		$this->assertInstanceOf('\Symfony\Component\HttpFoundation\JsonResponse', $stub);
		$this->assertEquals('{"foo":"foobar is awesome"}', $stub->getContent());
		$this->assertEquals('application/json', $stub->headers->get('content-type'));
	}
}