<?php
namespace AkkaFacebook\Test\TestCase\View\Helper;

use AkkaFacebook\View\Helper\GraphHelper;
use Cake\TestSuite\TestCase;
use Cake\View\View;

/**
 * AkkaFacebook\View\Helper\GraphHelper Test Case
 */
class GraphHelperTest extends TestCase
{

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $view = new View();
        $this->Graph = new GraphHelper($view);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Graph);

        parent::tearDown();
    }

    /**
     * Test initial setup
     *
     * @return void
     */
    public function testInitialization()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
