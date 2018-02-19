<?php

namespace Orchestra\Facile\Tests\Template;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Illuminate\Pagination\Paginator;
use Orchestra\Facile\Template\Simple;

class TemplateTest extends TestCase
{
    /**
     * Teardown the test environment.
     */
    protected function tearDown(): void
    {
        m::close();
    }

    /** @test */
    public function it_can_be_constructed()
    {
        $view = m::mock('\Illuminate\Contracts\View\Factory');

        $stub = new TemplateStub($view);
        $refl = new \ReflectionObject($stub);

        $formats = $refl->getProperty('formats');

        $formats->setAccessible(true);

        $this->assertEquals(['html', 'json', 'foo'], $formats->getValue($stub));
    }

    /** @test */
    public function it_can_compose_data()
    {
        $view = m::mock('\Illuminate\Contracts\View\Factory');

        $stub = new TemplateStub($view);
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
    public function it_return_406_status_when_given_invalid_format()
    {
        $view = m::mock('\Illuminate\Contracts\View\Factory');

        $view->shouldReceive('exists')->once()->with('errors.406')->andReturn(true)
            ->shouldReceive('make')->once()->with('errors.406', [])->andReturn('error-406');

        $stub = new TemplateStub($view);
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
     * @test
     * @expectedException \RuntimeException
     */
    public function it_throws_exception_when_rendering_method_is_not_available()
    {
        $view = m::mock('\Illuminate\Contracts\View\Factory');

        $stub = new TemplateStub($view);
        $data = [
            'view' => null,
            'data' => [],
            'status' => 200,
        ];

        $stub->compose('html', $data);
    }

    /** @test */
    public function it_can_transform_arrayable_instance_to_array()
    {
        $view = m::mock('\Illuminate\Contracts\View\Factory');
        $mock = m::mock('\Illuminate\Contracts\Support\Arrayable');

        $mock->shouldReceive('toArray')->once()->andReturn('foobar');

        $stub = new TemplateStub($view);
        $this->assertEquals('foobar', $stub->transformToArray($mock));
    }

    /** @test */
    public function it_can_transform_eloquent_instance_to_array()
    {
        $view = m::mock('\Illuminate\Contracts\View\Factory');
        $mock = m::mock('\Illuminate\Database\Eloquent\Model');

        $mock->shouldReceive('toArray')->once()->andReturn('foobar');

        $stub = new TemplateStub($view);
        $this->assertEquals('foobar', $stub->transformToArray($mock));
    }

    /** @test */
    public function it_can_transform_array()
    {
        $view = m::mock('\Illuminate\Contracts\View\Factory');
        $mock = m::mock('\Illuminate\Contracts\Support\Arrayable');

        $mock->shouldReceive('toArray')->once()->andReturn('foobar');

        $stub = new TemplateStub($view);
        $this->assertEquals(['foobar'], $stub->transformToArray([$mock]));
    }

    /** @test */
    public function it_can_transform_renderable_to_array()
    {
        $view = m::mock('\Illuminate\Contracts\View\Factory');
        $mock = m::mock('\Illuminate\Contracts\Support\Renderable');

        $mock->shouldReceive('render')->once()->andReturn('<foobar>');

        $stub = new TemplateStub($view);
        $this->assertEquals('&lt;foobar&gt;', $stub->transformToArray($mock));
    }

    /** @test */
    public function it_can_transform_paginator_instance_to_array()
    {
        $view = m::mock('\Illuminate\Contracts\View\Factory');
        $results = ['foo' => 'foobar'];

        $paginator = new Paginator($results, 3, 1);

        $stub = new TemplateStub($view);

        $expected = [
            'per_page' => 3,
            'current_page' => 1,
            'from' => 1,
            'to' => 1,
            'data' => $results,
            'first_page_url' => '/?page=1',
            'next_page_url' => null,
            'prev_page_url' => null,
            'path' => '/',
        ];

        $this->assertEquals($expected, $stub->transformToArray($paginator));
    }

    /** @test */
    public function it_can_get_supported_formats()
    {
        $view = m::mock('\Illuminate\Contracts\View\Factory');

        $stub = new TemplateStub($view);

        $this->assertContains('html', $stub->getSupportedFormats());
        $this->assertContains('json', $stub->getSupportedFormats());
        $this->assertContains('foo', $stub->getSupportedFormats());
        $this->assertNotContains('foobar', $stub->getSupportedFormats());
    }

    /**
     * @test
     * @dataProvider dataProviderForPrepareDataValue
     */
    public function it_can_compose_from_prepared_data($data, $expected)
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

class TemplateStub extends \Orchestra\Facile\Template\Template
{
    protected $formats = ['html', 'json', 'foo'];

    public function composeFoo()
    {
        return 'foo';
    }
}
