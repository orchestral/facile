<?php namespace Orchestra\Facile\Tests\Template;

use Mockery as m;
use Orchestra\Facile\Template\Base;

class BaseTest extends \PHPUnit_Framework_TestCase
{
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
        $request = m::mock('\Illuminate\Http\Request');
        $view    = m::mock('\Illuminate\View\Environment');

        $stub = new Base($request, $view);
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
        $request = m::mock('\Illuminate\Http\Request');
        $view    = m::mock('\Illuminate\View\Environment');

        $data = array('foo' => 'foo is awesome');

        $view->shouldReceive('make')->once()->with('users.index')->andReturn($view)
            ->shouldReceive('with')->with($data)->andReturn('foo');

        $stub = new Base($request, $view);

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
        $request = m::mock('\Illuminate\Http\Request');
        $view    = m::mock('\Illuminate\View\Environment');
        $data    = array('foo' => 'foobar is awesome');

        with(new Base($request, $view))->composeHtml(null, $data);
    }

    /**
     * Test Orchestra\Facile\Template::compose_json() method.
     *
     * @test
     */
    public function testComposeJsonMethod()
    {
        $request = m::mock('\Illuminate\Http\Request');
        $view    = m::mock('\Illuminate\View\Environment');
        $data    = array('foo' => 'foobar is awesome');

        $stub = with(new Base($request, $view))->composeJson(null, $data);

        $this->assertInstanceOf('\Illuminate\Http\JsonResponse', $stub);
        $this->assertEquals('{"foo":"foobar is awesome"}', $stub->getContent());
        $this->assertEquals('application/json', $stub->headers->get('content-type'));
    }
}
