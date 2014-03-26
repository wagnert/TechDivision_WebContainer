<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @category  AppServer
 * @package   TechDivision_WebContainer
 * @author    Bernhard Wick <b.wick@techdivision.com>
 * @copyright 2014 TechDivision GmbH - <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php
 *            Open Software License (OSL 3.0)
 * @link      http://www.techdivision.com/
 */

namespace TechDivision\WebContainer;

use TechDivision\ApplicationServer\Api\Node\AppserverNode;
use TechDivision\ApplicationServer\Configuration;
use TechDivision\ApplicationServer\InitialContext;
use TechDivision\ApplicationServer\Api\Node\ContainerNode;

/**
 * TechDivision\WebContainer\WebContainerTest
 *
 * Basic test class for the WebContainer class.
 *
 * @category  AppServer
 * @package   TechDivision_WebContainer
 * @author    Bernhard Wick <b.wick@techdivision.com>
 * @copyright 2014 TechDivision GmbH - <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php
 *            Open Software License (OSL 3.0)
 * @link      http://www.techdivision.com/
 */
class WebContainerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * The rewrite module instance to test.
     *
     * @var \TechDivision\WebContainer\Container
     */
    protected $container;

    /**
     * Initializes the rewrite module to test.
     *
     * @return void
     */
    public function setUp()
    {
        // Get a initial context
        $appserverNode = new AppserverNode();
        $appserverNode->initFromFile(__DIR__ . '/_files/appserver_initial_context.xml');

        // Get an empty node
        $containerNode = new ContainerNode();

        // Get an empty applications array as the webcontainer does not make use of it anyway
        $applications = array();

        $this->container = new Container($appserverNode, $containerNode, $applications);
    }

    /**
     * Test if the constructor created an instance of the rewrite module.
     *
     * @return void
     */
    public function testInstanceOf()
    {
        $this->assertInstanceOf('\TechDivision\WebContainer\Container', $this->container);
    }
}
