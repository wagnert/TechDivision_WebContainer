<?php
/**
 * TechDivision\WebServerContainer\Container
 *
 * PHP version 5
 *
 * @category  Appserver
 * @package   TechDivision_WebServerContainer
 * @author    Johann Zelger <jz@techdivision.com>
 * @copyright 2014 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.appserver.io
 */

namespace TechDivision\WebServerContainer;

use TechDivision\ApplicationServer\Interfaces\ContainerInterface;

/**
 * Class Container
 *
 * @category  Appserver
 * @package   TechDivision_WebServerContainer
 * @author    Johann Zelger <jz@techdivision.com>
 * @copyright 2014 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.appserver.io
 */
class Container extends \Stackable implements ContainerInterface
{
    /**
     * Initializes the container with the initial context, the unique container ID
     * and the deployed applications.
     *
     * @param \TechDivision\ApplicationServer\InitialContext                         $initialContext The initial context
     * @param \TechDivision\ApplicationServer\Api\Node\ContainerNode                 $containerNode  The container's UUID
     * @param array<\TechDivision\ApplicationServer\Interfaces\ApplicationInterface> $applications   The application instance
     *
     * @return void
     */
    public function __construct($initialContext, $containerNode, $applications)
    {
        $this->initialContext = $initialContext;
        $this->containerNode = $containerNode;
        $this->applications = $applications;
    }

    /**
     * Returns the receiver instance ready to be started.
     *
     * @return \TechDivision\ApplicationServer\Interfaces\ReceiverInterface The receiver instance
     */
    public function getReceiver()
    {
        // nothing
    }


    public function run()
    {

        error_log("JAAAAAAAAAAAAAAAAAAAAAA WOL");

    }

}
