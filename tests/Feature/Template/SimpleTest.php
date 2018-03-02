<?php

namespace Orchestra\Facile\Tests\Feature\Template;

use Mockery as m;
use Orchestra\Support\Collection;
use Orchestra\Facile\Template\Simple;
use Orchestra\Facile\TestCase\Feature\TestCase;

class SimpleTest extends TestCase
{
    /** @test */
    public function it_can_be_constructed()
    {
        $view = m::mock('\Illuminate\Contracts\View\Factory');

        $stub = new Simple($view);
        $refl = new \ReflectionObject($stub);

        $formats = $refl->getProperty('formats');

        $formats->setAccessible(true);

        $this->assertEquals(['csv', 'html', 'json', 'xml'], $formats->getValue($stub));
    }

    /** @test */
    public function it_can_compose_to_html()
    {
        $view = m::mock('\Illuminate\Contracts\View\Factory');
        $data = ['foo' => 'foo is awesome'];

        $view->shouldReceive('make')->once()->with('users.index')->andReturn($view)
            ->shouldReceive('with')->with($data)->andReturn('foo');

        $stub = new Simple($view);

        $this->assertInstanceOf('\Illuminate\Http\Response', $stub->composeHtml($data, 200, ['view' => 'users.index']));
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function it_throws_exception_when_composing_html_without_view()
    {
        $view = m::mock('\Illuminate\Contracts\View\Factory');
        $data = ['foo' => 'foobar is awesome'];

        with(new Simple($view))->composeHtml($data, 200, ['view' => null]);
    }

    /** @test */
    public function it_can_compose_to_json()
    {
        $view = m::mock('\Illuminate\Contracts\View\Factory');
        $data = ['foo' => 'foobar is awesome'];

        $stub = with(new Simple($view))->composeJson($data);

        $this->assertInstanceOf('\Illuminate\Http\Response', $stub);
        $this->assertEquals('{"foo":"foobar is awesome"}', $stub->getContent());
        $this->assertEquals('application/json', $stub->headers->get('content-type'));
    }

    /** @test */
    public function it_can_compose_to_csv_from_arrayable_interface()
    {
        $view = m::mock('\Illuminate\Contracts\View\Factory');

        $data = [
            'data' => new \Illuminate\Support\Collection([
                ['id' => 1, 'name' => 'Mior Muhammad Zaki'],
                ['id' => 2, 'name' => 'Taylor Otwell'],
            ]),
        ];

        $expected = <<<EXPECTED
id,name
1,"Mior Muhammad Zaki"
2,"Taylor Otwell"

EXPECTED;

        $stub = with(new Simple($view))->composeCsv($data);

        $this->assertInstanceOf('\Illuminate\Http\Response', $stub);
        $this->assertEquals($expected, $stub->getContent());
        $this->assertEquals('text/csv', $stub->headers->get('content-type'));
    }

    /** @test */
    public function it_can_compose_to_csv_from_csvable_interface()
    {
        $view = m::mock('\Illuminate\Contracts\View\Factory');
        $data = [
            'data' => new Collection([
                    ['id' => 1, 'name' => 'Mior Muhammad Zaki'],
                    ['id' => 2, 'name' => 'Taylor Otwell'],
                ]),
        ];

        $expected = <<<EXPECTED
id,name
1,"Mior Muhammad Zaki"
2,"Taylor Otwell"

EXPECTED;

        $stub = with(new Simple($view))->composeCsv($data);

        $this->assertInstanceOf('\Illuminate\Http\Response', $stub);
        $this->assertEquals($expected, $stub->getContent());
        $this->assertEquals('text/csv', $stub->headers->get('content-type'));
    }
}
