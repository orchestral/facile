<?php namespace Orchestra\Facile\Tests;

class EnvironmentTest extends \PHPUnit_Framework_TestCase {

	/**
	 * Test construct an instance of Orchestra\Facile\Environment.
	 *
	 * @test
	 */
	public function testConstructMethod()
	{
		$stub = new \Orchestra\Facile\Environment;

		$refl      = new \ReflectionObject($stub);
		$templates = $refl->getProperty('templates');
		$templates->setAccessible(true);

		$this->assertTrue(is_array($templates->getValue($stub)));
	}

	/**
	 * Test Orchestra\Facile\Environment::template() method.
	 *
	 * @test
	 */
	public function testTemplateMethod()
	{
		$stub = new \Orchestra\Facile\Environment;

		$refl      = new \ReflectionObject($stub);
		$templates = $refl->getProperty('templates');
		$templates->setAccessible(true);

		$this->assertTrue(is_array($templates->getValue($stub)));

		$stub->template('foo', new FooTemplateStub);
	}

	/**
	 * Test Orchestra\Facile\Environment::template() method throws exception 
	 * when template is not instanceof \Orchestra\Facile\TemplateDriver
	 *
	 * @test
	 * @expectedException RuntimeException
	 */
	public function testTemplateMethodThrowsException()
	{
		$stub = new \Orchestra\Facile\Environment;
		$stub->template('badFoo', new BadFooTemplateStub);
	}
}

class FooTemplateStub extends \Orchestra\Facile\TemplateDriver {}

class BadFooTemplateStub {}