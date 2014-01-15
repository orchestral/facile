<?php namespace Orchestra\Facile\Tests\Template;

use Mockery as m;
use Illuminate\Pagination\Paginator;

class DriverTest extends \PHPUnit_Framework_TestCase
{
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
        $view = m::mock('\Illuminate\View\Factory');

        $stub = new TemplateDriverStub($view);
        $refl = new \ReflectionObject($stub);

        $formats       = $refl->getProperty('formats');
        $defaultFormat = $refl->getProperty('defaultFormat');

        $formats->setAccessible(true);
        $defaultFormat->setAccessible(true);

        $this->assertEquals(array('html', 'json', 'foo'), $formats->getValue($stub));
        $this->assertEquals('html', $defaultFormat->getValue($stub));
    }

    /**
     * Test Orchestra\Facile\Template\Driver::getDefaultFormat() method
     *
     * @test
     */
    public function testGetDefaultFormatMethod()
    {
        $view = m::mock('\Illuminate\View\Factory');

        $stub = new TemplateDriverStub($view);

        $this->assertEquals('html', $stub->getDefaultFormat());
    }

    /**
     * Test Orchestra\Facile\Template\Driver::compose() method.
     *
     * @test
     */
    public function testComposeMethod()
    {
        $view = m::mock('\Illuminate\View\Factory');

        $stub = new TemplateDriverStub($view);
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
        $view = m::mock('\Illuminate\View\Factory');

        $view->shouldReceive('exists')->once()->with('error.406')->andReturn(true)
            ->shouldReceive('make')->once()->with('error.406', array())->andReturn('error-406');

        $stub = new TemplateDriverStub($view);
        $data = array(
            'view'   => null,
            'data'   => array(),
            'status' => 200,
        );

        $response = $stub->compose('foobar', $data);
        $this->assertInstanceOf("\Illuminate\Http\Response", $response);
        $this->assertEquals('error-406', $response->getContent());
    }

    /**
     * Test Orchestra\Facile\Template\Driver::compose() method throws exception
     * when given method isn't available.
     *
     * @expectedException \RuntimeException
     */
    public function testComposeMethodThrowsExceptionWhenMethodNotAvailable()
    {
        $view = m::mock('\Illuminate\View\Factory');

        $stub = new TemplateDriverStub($view);
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
        $view = m::mock('\Illuminate\View\Factory');
        $mock = m::mock('\Illuminate\Support\Contracts\ArrayableInterface');

        $mock->shouldReceive('toArray')->once()->andReturn('foobar');

        $stub = new TemplateDriverStub($view);
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
        $view = m::mock('\Illuminate\View\Factory');
        $mock = m::mock('\Illuminate\Database\Eloquent\Model');

        $mock->shouldReceive('toArray')->once()->andReturn('foobar');

        $stub = new TemplateDriverStub($view);
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
        $view = m::mock('\Illuminate\View\Factory');
        $mock = m::mock('\Illuminate\Support\Contracts\ArrayableInterface');

        $mock->shouldReceive('toArray')->once()->andReturn('foobar');

        $stub = new TemplateDriverStub($view);
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
        $view = m::mock('\Illuminate\View\Factory');
        $mock = m::mock('\Illuminate\Support\Contracts\RenderableInterface');

        $mock->shouldReceive('render')->once()->andReturn('<foobar>');

        $stub = new TemplateDriverStub($view);
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
        $view    = m::mock('\Illuminate\View\Factory');
        $env     = m::mock('\Illuminate\Pagination\Factory');
        $results = array('foo' => 'foobar');

        $env->shouldReceive('getCurrentPage')->once()->andReturn(1);

        $paginator = new Paginator($env, $results, 3, 1);
        $paginator->setupPaginationContext();

        $stub = new TemplateDriverStub($view);

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

class TemplateDriverStub extends \Orchestra\Facile\Template\Driver
{
    protected $formats = array('html', 'json', 'foo');

    public function composeFoo()
    {
        return 'foo';
    }
}
