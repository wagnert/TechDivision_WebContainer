<?php
/**
 * TechDivision\WebServerContainer\Deployment
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

use TechDivision\ApplicationServer\AbstractDeployment;

/**
 * Class Deployment
 *
 * @category  Appserver
 * @package   TechDivision_WebServerContainer
 * @author    Johann Zelger <jz@techdivision.com>
 * @copyright 2014 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.appserver.io
 */
class Deployment extends AbstractDeployment
{

    /**
     * Returns an array with available applications.
     *
     * @return \TechDivision\ApplicationServer\Interfaces\DeploymentInterface The deployment instance
     */
    public function deploy()
    {
        return $this;
    }
}
