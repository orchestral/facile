<?php namespace Orchestra\Facile\TestCase;

use Mockery as m;
use Orchestra\Facile\Facile;
use Orchestra\Facile\Factory;
use Orchestra\Facile\Template\Simple;

class FacileTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Teardown the test environment.
     */
    public function tearDown()
    {
        m::close();
    }

    /**
     * Test construct an instance of Orchestra\Facile\Container.
     *
     * @test
     */
    public function testConstructMethod()
    {
        $request = m::mock('\Illuminate\Http\Request');
        $view    = m::mock('\Illuminate\Contracts\View\Factory');

        $stub = new Facile(new Factory($request), new Simple($view), [], 'json');

        $refl = new \ReflectionObject($stub);
        $data = $refl->getProperty('data');
        $data->setAccessible(true);

        $expected = [
            'view'   => null,
            'data'   => [],
            'status' => 200,
            'on'     => [
                'html' => ['only' => null, 'except' => null],
                'json' => ['only' => null, 'except' => null],
                'csv'  => ['uses' => 'data'],
            ],
        ];

        $this->assertEquals($expected, $data->getValue($stub));
    }

    /**
     * Test Orchestra\Facile\Container::view() method.
     *
     * @test
     */
    public function testViewMethod()
    {
        $request = m::mock('\Illuminate\Http\Request');
        $view    = m::mock('\Illuminate\Contracts\View\Factory');

        $stub = new Facile(new Factory($request), new Simple($view), [], 'json');

        $stub->view('foo.bar');

        $refl = new \ReflectionObject($stub);
        $data = $refl->getProperty('data');
        $data->setAccessible(true);

        $result = $data->getValue($stub);

        $this->assertEquals('foo.bar', $result['view']);
    }

    /**
     * Test Orchestra\Facile\Container::when() method.
     *
     * @test
     * @dataProvider dataProviderForWhenTest
     */
    public function testWhenMethod($before, $after)
    {
        $request = m::mock('\Illuminate\Http\Request');
        $view    = m::mock('\Illuminate\Contracts\View\Factory');

        $stub = new Facile(new Factory($request), new Simple($view), [], 'json');

        $refl = new \ReflectionObject($stub);
        $data = $refl->getProperty('data');
        $data->setAccessible(true);

        $this->assertEquals($before, $data->getValue($stub));

        $stub->when('foo', ['uses' => 'foobar']);

        $this->assertEquals($after, $data->getValue($stub));
    }

    /**
     * Test Orchestra\Facile\Container::on() method.
     *
     * @test
     * @dataProvider dataProviderForWhenTest
     */
    public function testOnMethod($before, $after)
    {
        $request = m::mock('\Illuminate\Http\Request');
        $view    = m::mock('\Illuminate\Contracts\View\Factory');

        $stub = new Facile(new Factory($request), new Simple($view), [], 'json');

        $refl = new \ReflectionObject($stub);
        $data = $refl->getProperty('data');
        $data->setAccessible(true);

        $this->assertEquals($before, $data->getValue($stub));

        $stub->on('foo', ['uses' => 'foobar']);

        $this->assertEquals($after, $data->getValue($stub));
    }

    /**
     * Test Orchestra\Facile\Container::with() method.
     *
     * @test
     */
    public function testWithMethod()
    {
        $request = m::mock('\Illuminate\Http\Request');
        $view    = m::mock('\Illuminate\Contracts\View\Factory');

        $stub = new Facile(new Factory($request), new Simple($view), [], 'json');

        $stub->with('foo', 'bar');
        $stub->with(['foobar' => 'foo']);

        $refl = new \ReflectionObject($stub);
        $data = $refl->getProperty('data');
        $data->setAccessible(true);

        $result = $data->getValue($stub);

        $this->assertEquals(['foo' => 'bar', 'foobar' => 'foo'], $result['data']);
    }

    /**
     * Test Orchestra\Facile\Container::status() method.
     *
     * @test
     */
    public function testStatusMethod()
    {
        $request = m::mock('\Illuminate\Http\Request');
        $view    = m::mock('\Illuminate\Contracts\View\Factory');

        $stub = new Facile(new Factory($request), new Simple($view), [], 'json');

        $stub->status(500);

        $refl = new \ReflectionObject($stub);
        $data = $refl->getProperty('data');
        $data->setAccessible(true);

        $result = $data->getValue($stub);

        $this->assertEquals(500, $result['status']);
    }

    /**
     * Test Orchestra\Facile\Container::template() method.
     *
     * @test
     */
    public function testTemplateMethod()
    {
        $request  = m::mock('\Illuminate\Http\Request');
        $view     = m::mock('\Illuminate\Contracts\View\Factory');

        $env      = new Factory($request);
        $template = new Simple($view);

        $env->template('foo', $template);

        $stub = new Facile($env, $template, [], 'json');

        $stub->template('foo');

        $refl     = new \ReflectionObject($stub);
        $template = $refl->getProperty('template');
        $template->setAccessible(true);

        $this->assertEquals('foo', $template->getValue($stub));

        $stub->template(new Simple($view));

        $refl     = new \ReflectionObject($stub);
        $template = $refl->getProperty('template');
        $template->setAccessible(true);

        $this->assertStringStartsWith('template-', $template->getValue($stub));
    }

    /**
     * Test Orchestra\Facile\Container::format() method.
     *
     * @test
     */
    public function testFormatMethod()
    {
        $request  = m::mock('\Illuminate\Http\Request');
        $template = m::mock('\Orchestra\Facile\Template\Simple');

        $request->shouldReceive('prefers')->once()->with('jsonp')->andReturn('jsonp');
        $template->shouldReceive('getSupportedFormats')->once()->andReturn('jsonp');

        $stub = new Facile(new Factory($request), $template, [], null);

        $this->assertEquals('jsonp', $stub->getFormat());

        $stub->format('md');

        $refl   = new \ReflectionObject($stub);
        $format = $refl->getProperty('format');
        $format->setAccessible(true);

        $this->assertEquals('md', $format->getValue($stub));
    }

    /**
     * Test Orchestra\Facile\Container::render() method.
     *
     * @test
     */
    public function testRenderMethod()
    {
        $request  = m::mock('\Illuminate\Http\Request');
        $template = m::mock('\Orchestra\Facile\Template\Simple');

        $request->shouldReceive('prefers')->once()->with('jsonp')->andReturn('jsonp');
        $template->shouldReceive('compose')->once()->andReturn('foo')
            ->shouldReceive('getSupportedFormats')->once()->andReturn('jsonp');

        $stub = new Facile(new Factory($request), $template, []);

        $this->assertEquals('foo', $stub->render());
    }

    /**
     * Test Orchestra\Facile\Container::__toString() method.
     *
     * @test
     */
    public function testToStringMethod()
    {
        $request   = m::mock('\Illuminate\Http\Request');
        $template1 = m::mock('\Orchestra\Facile\Template\Simple');

        $template1->shouldReceive('compose')->once()
                ->with('json', m::any())->andReturn(json_encode(['foo' => 'foo is awesome']));

        $stub1 = new Facile(new Factory($request), $template1, [], 'json');

        ob_start();
        echo $stub1;
        $output1 = ob_get_contents();
        ob_end_clean();

        $this->assertEquals('{"foo":"foo is awesome"}', $output1);

        $render = m::mock('\Illuminate\Contracts\Support\Renderable');
        $render->shouldReceive('render')->once()->andReturn('foo is awesome');

        $template2 = m::mock('\Orchestra\Facile\Template\Template');
        $template2->shouldReceive('compose')->once()->with('json', m::any())->andReturn($render);

        $stub2 = new Facile(new Factory($request), $template2, [], 'json');

        ob_start();
        echo $stub2;
        $output2 = ob_get_contents();
        ob_end_clean();

        $this->assertEquals('foo is awesome', $output2);
    }

    /**
     * Data provider for prepare data value test.
     */
    public function dataProviderForWhenTest()
    {
        return [
            [
                [
                    'view'   => null,
                    'data'   => [],
                    'status' => 200,
                    'on'     => [
                        'html' => ['only' => null, 'except' => null],
                        'json' => ['only' => null, 'except' => null],
                        'csv'  => ['uses' => 'data'],
                    ],
                ],
                [
                    'view'   => null,
                    'data'   => [],
                    'status' => 200,
                    'on'     => [
                        'html' => ['only' => null, 'except' => null],
                        'json' => ['only' => null, 'except' => null],
                        'csv'  => ['uses' => 'data'],
                        'foo'  => ['uses' => 'foobar'],
                    ],
                ],
            ],
        ];
    }
}
