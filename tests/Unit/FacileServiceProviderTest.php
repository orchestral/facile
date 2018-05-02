<?php

namespace Orchestra\Facile\TestCase\Unit;

use PHPUnit\Framework\TestCase;
use Orchestra\Facile\FacileServiceProvider;

class FacileServiceProviderTest extends TestCase
{
    /** @test */
    public function it_deferred_registering_the_services()
    {
        $stub = new FacileServiceProvider(null);

        $this->assertTrue($stub->isDeferred());
    }

    /** @test */
    public function it_can_provides_expected_services()
    {
        $stub = new FacileServiceProvider(null);

        $this->assertContains('orchestra.facile', $stub->provides());
    }
}
