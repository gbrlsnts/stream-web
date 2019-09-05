<?php

namespace Tests\Functional;

class HomepageTest extends BaseTestCase
{
    /**
     * Test that the index route returns a rendered response containing the text 'SlimFramework' but not a greeting
     */
    public function testGetHomepageWithoutName()
    {
        $response = $this->runApp('GET', '/');

        $this->assertEquals(302, $response->getStatusCode());
    }

        /**
     * Test that the index route returns a rendered response containing the text 'SlimFramework' but not a greeting
     */
    public function testDefaultStreamPage()
    {
        $response = $this->runApp('GET', '/s/ezstream');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('ezstream', (string)$response->getBody());
        $this->assertStringNotContainsString('index', (string)$response->getBody());
    }
}
