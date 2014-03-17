<?php

/**
 * TechDivision\WebContainer\WebContainerDeployment
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @category  Appserver
 * @package   TechDivision_WebContainer
 * @author    Tim Wagner <tw@techdivision.com>
 * @copyright 2014 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.appserver.io
 */

namespace TechDivision\WebContainer;

use TechDivision\ApplicationServer\AbstractDeployment;

/**
 * Specific deployment implementation for web applications.
 *
 * @category  Appserver
 * @package   TechDivision_WebContainer
 * @author    Tim Wagner <tw@techdivision.com>
 * @copyright 2014 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.appserver.io
 */
class WebContainerDeployment extends AbstractDeployment
{

    /**
     * Returns an array with available applications.
     *
     * @return \TechDivision\ApplicationServer\Interfaces\DeploymentInterface The deployment instance
     */
    public function deploy()
    {
        
        // gather all the deployed web applications
        foreach (new \FilesystemIterator($this->getBaseDirectory($this->getAppBase())) as $folder) {
            
            // check if file or subdirectory has been found
            if ($folder->isDir() === true) {
                
                // initialize the application instance
                $application = new WebApplication(
                    $this->getInitialContext(),
                    $this->getContainerNode(),
                    $folder->getBasename()
                );

                // add the application to the available applications
                $this->addApplication($application);
            }
        }

        // return initialized applications
        return $this;
    }
}
