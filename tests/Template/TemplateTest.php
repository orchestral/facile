<?php namespace Orchestra\Facile\Tests\Template;

use Mockery as m;
use Illuminate\Pagination\Paginator;
use Orchestra\Facile\Template\Simple;

class TemplateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Teardown the test environment.
     */
    public function tearDown()
    {
        m::close();
    }

    /**
     * Test construct an instance of Orchestra\Facile\Template\Driver.
     *
     * @test
     */
    public function testConstructMethod()
    {
        $view = m::mock('\Illuminate\Contracts\View\Factory');

        $stub = new TemplateTemplateStub($view);
        $refl = new \ReflectionObject($stub);

        $formats = $refl->getProperty('formats');

        $formats->setAccessible(true);

        $this->assertEquals(['html', 'json', 'foo'], $formats->getValue($stub));
    }

    /**
     * Test Orchestra\Facile\Template\Driver::compose() method.
     *
     * @test
     */
    public function testComposeMethod()
    {
        $view = m::mock('\Illuminate\Contracts\View\Factory');

        $stub = new TemplateTemplateStub($view);
        $data = [
            'view' => null,
            'data' => [],
            'status' => 200,
        ];

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
        $view = m::mock('\Illuminate\Contracts\View\Factory');

        $view->shouldReceive('exists')->once()->with('errors.406')->andReturn(true)
            ->shouldReceive('make')->once()->with('errors.406', [])->andReturn('error-406');

        $stub = new TemplateTemplateStub($view);
        $data = [
            'view' => null,
            'data' => [],
            'status' => 200,
        ];

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
        $view = m::mock('\Illuminate\Contracts\View\Factory');

        $stub = new TemplateTemplateStub($view);
        $data = [
            'view' => null,
            'data' => [],
            'status' => 200,
        ];

        $stub->compose('html', $data);
    }

    /**
     * Test Orchestra\Facile\Template\Driver::transformToArray() method when
     * item has toArray().
     *
     * @test
     */
    public function testTransformToArrayMethodWhenItemHasToArray()
    {
        $view = m::mock('\Illuminate\Contracts\View\Factory');
        $mock = m::mock('\Illuminate\Contracts\Support\Arrayable');

        $mock->shouldReceive('toArray')->once()->andReturn('foobar');

        $stub = new TemplateTemplateStub($view);
        $this->assertEquals('foobar', $stub->transformToArray($mock));
    }

    /**
     * Test Orchestra\Facile\Template\Driver::transformToArray() method when
     * item is instance of Illuminate\Database\Eloquent\Model.
     *
     * @test
     */
    public function testTransformToArrayMethodWhenItemIsInstanceOfEloquent()
    {
        $view = m::mock('\Illuminate\Contracts\View\Factory');
        $mock = m::mock('\Illuminate\Database\Eloquent\Model');

        $mock->shouldReceive('toArray')->once()->andReturn('foobar');

        $stub = new TemplateTemplateStub($view);
        $this->assertEquals('foobar', $stub->transformToArray($mock));
    }

    /**
     * Test Orchestra\Facile\Template\Driver::transformToArray() method when
     * item is an array.
     *
     * @test
     */
    public function testTransformToArrayMethodWhenItemIsArray()
    {
        $view = m::mock('\Illuminate\Contracts\View\Factory');
        $mock = m::mock('\Illuminate\Contracts\Support\Arrayable');

        $mock->shouldReceive('toArray')->once()->andReturn('foobar');

        $stub = new TemplateTemplateStub($view);
        $this->assertEquals(['foobar'], $stub->transformToArray([$mock]));
    }

    /**
     * Test Orchestra\Facile\Template\Driver::transformToArray() method when
     * item has renderable.
     *
     * @test
     */
    public function testTransformToArrayMethodWhenItemIsRenderable()
    {
        $view = m::mock('\Illuminate\Contracts\View\Factory');
        $mock = m::mock('\Illuminate\Contracts\Support\Renderable');

        $mock->shouldReceive('render')->once()->andReturn('<foobar>');

        $stub = new TemplateTemplateStub($view);
        $this->assertEquals('&lt;foobar&gt;', $stub->transformToArray($mock));
    }

    /**
     * Test Orchestra\Facile\Template\Driver::transformToArray() method
     * when item is instance of Paginator.
     *
     * @test
     */
    public function testTransformToArrayMethodWhenItemInstanceOfPaginator()
    {
        $view = m::mock('\Illuminate\Contracts\View\Factory');
        $results = ['foo' => 'foobar'];

        $paginator = new Paginator($results, 3, 1);

        $stub = new TemplateTemplateStub($view);

        $expected = [
            'per_page' => 3,
            'current_page' => 1,
            'from' => 1,
            'to' => 1,
            'data' => $results,
            'next_page_url' => null,
            'prev_page_url' => null,
        ];

        $this->assertEquals($expected, $stub->transformToArray($paginator));
    }

    public function testGetSupportedFormatsMethod()
    {
        $view = m::mock('\Illuminate\Contracts\View\Factory');

        $stub = new TemplateTemplateStub($view);

        $this->assertContains('html', $stub->getSupportedFormats());
        $this->assertContains('json', $stub->getSupportedFormats());
        $this->assertContains('foo', $stub->getSupportedFormats());
        $this->assertNotContains('foobar', $stub->getSupportedFormats());
    }

    /**
     * Test Orchestra\Facile\Template\Driver::prepareDataValue() method.
     *
     * @test
     * @dataProvider dataProviderForPrepareDataValue
     */
    public function testPrepareDataValueMethod($data, $expected)
    {
        $view = m::mock('\Illuminate\Contracts\View\Factory');

        $stub = new Simple($view);

        $response = $stub->compose('json', $data);
        $this->assertEquals($expected, $response->getContent());
    }

    /**
     * Data provider for prepare data value test.
     */
    public function dataProviderForPrepareDataValue()
    {
        $data = ['foo' => 'foobar', 'hello' => 'world', 'laravel' => 'awesome'];

        return [
            [
                [
                    'view' => null,
                    'data' => $data,
                    'on' => [
                        'json' => ['only' => ['foo']],
                    ],
                    'status' => 200,
                ],
                '{"foo":"foobar"}',
            ],
            [
                [
                    'view' => null,
                    'data' => $data,
                    'on' => [
                        'json' => ['except' => ['foo']],
                    ],
                    'status' => 200,
                ],
                '{"hello":"world","laravel":"awesome"}',
            ],
        ];
    }
}

class TemplateTemplateStub extends \Orchestra\Facile\Template\Template
{
    protected $formats = ['html', 'json', 'foo'];

    public function composeFoo()
    {
        return 'foo';
    }
}
