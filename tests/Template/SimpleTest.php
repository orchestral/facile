<?php namespace Orchestra\Facile\Tests\Template;

use Mockery as m;
use Orchestra\Support\Collection;
use Orchestra\Facile\Template\Simple;

class SimpleTest extends \PHPUnit_Framework_TestCase
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
        $view = m::mock('\Illuminate\Contracts\View\Factory');

        $stub = new Simple($view);
        $refl = new \ReflectionObject($stub);

        $formats       = $refl->getProperty('formats');
        $defaultFormat = $refl->getProperty('defaultFormat');

        $formats->setAccessible(true);
        $defaultFormat->setAccessible(true);

        $this->assertEquals(array('html', 'json', 'csv'), $formats->getValue($stub));
        $this->assertEquals('html', $defaultFormat->getValue($stub));
    }

    /**
     * Test Orchestra\Facile\Template::composeHtml() method.
     *
     * @test
     */
    public function testComposeHtmlMethod()
    {
        $view = m::mock('\Illuminate\Contracts\View\Factory');
        $data = array('foo' => 'foo is awesome');

        $view->shouldReceive('make')->once()->with('users.index')->andReturn($view)
            ->shouldReceive('with')->with($data)->andReturn('foo');

        $stub = new Simple($view);

        $this->assertInstanceOf('\Illuminate\Http\Response', $stub->composeHtml('users.index', $data));
    }

    /**
     * Test Orchestra\Facile\Template::composeHtml() method throws exception
     * when view is not defined
     *
     * @expectedException \InvalidArgumentException
     */
    public function testComposeHtmlMethodThrowsException()
    {
        $view = m::mock('\Illuminate\Contracts\View\Factory');
        $data = array('foo' => 'foobar is awesome');

        with(new Simple($view))->composeHtml(null, $data);
    }

    /**
     * Test Orchestra\Facile\Template::composeJson() method.
     *
     * @test
     */
    public function testComposeJsonMethod()
    {
        $view = m::mock('\Illuminate\Contracts\View\Factory');
        $data = array('foo' => 'foobar is awesome');

        $stub = with(new Simple($view))->composeJson(null, $data);

        $this->assertInstanceOf('\Illuminate\Http\JsonResponse', $stub);
        $this->assertEquals('{"foo":"foobar is awesome"}', $stub->getContent());
        $this->assertEquals('application/json', $stub->headers->get('content-type'));
    }

    /**
     * Test Orchestra\Facile\Template::composeCsv() method
     * given as Illuminate\Contracts\Support\Arrayable.
     *
     * @test
     */
    public function testComposeCsvMethodAsArrayableInterface()
    {
        $view = m::mock('\Illuminate\Contracts\View\Factory');

        $data = array(
            'data' => new \Illuminate\Support\Collection(array(
                array('id' => 1, 'name' => 'Mior Muhammad Zaki'),
                array('id' => 2, 'name' => 'Taylor Otwell'),
            )),
        );

        $expected = <<<EXPECTED
id,name
1,"Mior Muhammad Zaki"
2,"Taylor Otwell"

EXPECTED;

        $stub = with(new Simple($view))->composeCsv(null, $data);

        $this->assertInstanceOf('\Illuminate\Http\Response', $stub);
        $this->assertEquals($expected, $stub->getContent());
        $this->assertEquals('text/csv', $stub->headers->get('content-type'));
    }

    /**
     * Test Orchestra\Facile\Template::composeCsv() method
     * given as Orchestra\Support\Contracts\CsvableInterface.
     *
     * @test
     */
    public function testComposeCsvMethodAsCsvableInterface()
    {
        $view = m::mock('\Illuminate\Contracts\View\Factory');
        $data = array(
            'data' => new Collection(array(
                    array('id' => 1, 'name' => 'Mior Muhammad Zaki'),
                    array('id' => 2, 'name' => 'Taylor Otwell'),
                )),
        );

        $expected = <<<EXPECTED
id,name
1,"Mior Muhammad Zaki"
2,"Taylor Otwell"

EXPECTED;

        $stub = with(new Simple($view))->composeCsv(null, $data);

        $this->assertInstanceOf('\Illuminate\Http\Response', $stub);
        $this->assertEquals($expected, $stub->getContent());
        $this->assertEquals('text/csv', $stub->headers->get('content-type'));
    }
}
