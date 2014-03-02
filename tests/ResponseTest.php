<?php namespace Orchestra\Facile\TestCase;

use Mockery as m;
use Orchestra\Facile\Response;
use Orchestra\Facile\Environment;
use Orchestra\Facile\Template\Base;

class ResponseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Teardown the test environment.
     */
    public function tearDown()
    {
        m::close();
    }

    /**
     * Test construct an instance of Orchestra\Facile\Response.
     *
     * @test
     */
    public function testConstructMethod()
    {
        $request = m::mock('\Illuminate\Http\Request');
        $view    = m::mock('\Illuminate\View\Environment');

        $stub = new Response(new Environment($request), new Base($view), array(), 'json');

        $refl = new \ReflectionObject($stub);
        $data = $refl->getProperty('data');
        $data->setAccessible(true);

        $expected = array('view' => null, 'data' => array(), 'status' => 200);

        $this->assertEquals($expected, $data->getValue($stub));
    }

    /**
     * Test Orchestra\Facile\Response::view() method.
     *
     * @test
     */
    public function testViewMethod()
    {
        $request = m::mock('\Illuminate\Http\Request');
        $view    = m::mock('\Illuminate\View\Environment');

        $stub = new Response(new Environment($request), new Base($view), array(), 'json');

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
        $request = m::mock('\Illuminate\Http\Request');
        $view    = m::mock('\Illuminate\View\Environment');

        $stub = new Response(new Environment($request), new Base($view), array(), 'json');

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
        $request = m::mock('\Illuminate\Http\Request');
        $view    = m::mock('\Illuminate\View\Environment');

        $stub = new Response(new Environment($request), new Base($view), array(), 'json');

        $stub->status(500);

        $refl = new \ReflectionObject($stub);
        $data = $refl->getProperty('data');
        $data->setAccessible(true);

        $result = $data->getValue($stub);

        $this->assertEquals(500, $result['status']);
    }

    /**
     * Test Orchestra\Facile\Response::template() method.
     *
     * @test
     */
    public function testTemplateMethod()
    {
        $request  = m::mock('\Illuminate\Http\Request');
        $view     = m::mock('\Illuminate\View\Environment');

        $env      = new Environment($request);
        $template = new Base($view);

        $env->template('foo', $template);

        $stub = new Response($env, $template, array(), 'json');

        $stub->template('foo');

        $refl     = new \ReflectionObject($stub);
        $template = $refl->getProperty('template');
        $template->setAccessible(true);

        $this->assertEquals('foo', $template->getValue($stub));

        $stub->template(new Base($view));

        $refl     = new \ReflectionObject($stub);
        $template = $refl->getProperty('template');
        $template->setAccessible(true);

        $this->assertStringStartsWith('template-', $template->getValue($stub));
    }

    /**
     * Test Orchestra\Facile\Response::format() method.
     *
     * @test
     */
    public function testFormatMethod()
    {
        $request  = m::mock('\Illuminate\Http\Request');
        $template = m::mock('\Orchestra\Facile\Template\Base');

        $request->shouldReceive('format')->once()->with('jsonp')->andReturn('jsonp');
        $template->shouldReceive('getDefaultFormat')->once()->andReturn('jsonp');

        $stub = new Response(new Environment($request), $template, array(), null);

        $this->assertEquals('jsonp', $stub->getFormat());

        $stub->format('md');

        $refl   = new \ReflectionObject($stub);
        $format = $refl->getProperty('format');
        $format->setAccessible(true);

        $this->assertEquals('md', $format->getValue($stub));
    }

    /**
     * Test Orchestra\Facile\Response::render() method.
     *
     * @test
     */
    public function testRenderMethod()
    {
        $request  = m::mock('\Illuminate\Http\Request');
        $template = m::mock('\Orchestra\Facile\Template\Base');

        $request->shouldReceive('format')->once()->with('jsonp')->andReturn('jsonp');
        $template->shouldReceive('compose')->once()->andReturn('foo')
            ->shouldReceive('getDefaultFormat')->once()->andReturn('jsonp');

        $stub = new Response(new Environment($request), $template, array());

        $this->assertEquals('foo', $stub->render());
    }

    /**
     * Test Orchestra\Facile\Response::__toString() method.
     *
     * @test
     */
    public function testToStringMethod()
    {
        $request   = m::mock('\Illuminate\Http\Request');
        $template1 = m::mock('\Orchestra\Facile\Template\Base');

        $template1->shouldReceive('compose')->once()
                ->with('json', m::any())->andReturn(json_encode(array('foo' => 'foo is awesome')));

        $stub1 = new Response(new Environment($request), $template1, array(), 'json');

        ob_start();
        echo $stub1;
        $output1 = ob_get_contents();
        ob_end_clean();

        $this->assertEquals('{"foo":"foo is awesome"}', $output1);

        $render = m::mock('\Illuminate\Support\Contracts\RenderableInterface');
        $render->shouldReceive('render')->once()->andReturn('foo is awesome');

        $template2 = m::mock('\Orchestra\Facile\Template\Driver');
        $template2->shouldReceive('compose')->once()->with('json', m::any())->andReturn($render);

        $stub2 = new Response(new Environment($request), $template2, array(), 'json');

        ob_start();
        echo $stub2;
        $output2 = ob_get_contents();
        ob_end_clean();

        $this->assertEquals('foo is awesome', $output2);
    }
}
