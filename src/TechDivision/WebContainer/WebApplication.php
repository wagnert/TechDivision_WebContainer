<?php

/**
 * TechDivision\WebContainer\WebApplication
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

use TechDivision\Servlet\Servlet;
use TechDivision\Servlet\ServletContext;
use TechDivision\Servlet\Http\HttpServletRequest;
use TechDivision\ApplicationServer\AbstractApplication;
use TechDivision\ApplicationServer\Api\ContainerService;
use TechDivision\ApplicationServer\Api\Node\AppNode;
use TechDivision\ApplicationServer\Api\Node\NodeInterface;
use TechDivision\ApplicationServer\Interfaces\ContextInterface;

/**
 * The application instance holds all information about the deployed application
 * and provides a reference to the servlet manager and the initial context.
 *
 * @category  Appserver
 * @package   TechDivision_WebContainer
 * @author    Tim Wagner <tw@techdivision.com>
 * @copyright 2014 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.appserver.io
 */
class WebApplication extends AbstractApplication
{

    /**
     * Has been automatically invoked by the container after the application
     * instance has been created.
     *
     * @return \TechDivision\ApplicationServer\Interfaces\ApplicationInterface The connected application
     */
    public function connect()
    {

        try {
            
            // initialize the class loader with the additional folders
            set_include_path(get_include_path() . PATH_SEPARATOR . $this->getWebappPath());
            set_include_path(get_include_path() . PATH_SEPARATOR . $this->getWebappPath() . DIRECTORY_SEPARATOR . 'WEB-INF' . DIRECTORY_SEPARATOR . 'classes');
            set_include_path(get_include_path() . PATH_SEPARATOR . $this->getWebappPath() . DIRECTORY_SEPARATOR . 'WEB-INF' . DIRECTORY_SEPARATOR . 'lib');
    
            // initialize the servlet manager instance
            $servletContext = new ServletManager($this);
    
            // set the servlet manager
            $this->setServletContext($servletContext->initialize());
            
            // initialize the servlet locator instance
            $servletLocator = new ServletLocator($this->getServletContext());
            
            // set the servlet locator
            $this->setServletLocator($servletLocator);
            
        } catch (InvalidApplicationArchiveException $iaae) {
            // do nothing here, we simple doesn't have a web application
        }

        // return the instance itself
        return $this;
    }
    
    /**
     * Bounds the application to the passed virtual host.
     * 
     * @param \TechDivision\WebContainer\VirtualHost $virtualHost The virtual host to add
     * 
     * @return void
     */
    public function addVirtualHost(VirtualHost $virtualHost)
    {
        $this->vhosts[] = $virtualHost;
    }

    /**
     * Sets the applications servlet context instance.
     *
     * @param \TechDivision\Servlet\ServletContext $servletContext The servlet context instance
     *
     * @return void
     */
    public function setServletContext(ServletContext $servletContext)
    {
        $this->servletContext = $servletContext;
    }

    /**
     * Return the servlet context instance.
     *
     * @return \TechDivision\Servlet\ServletContext The servlet context instance
     */
    public function getServletContext()
    {
        return $this->servletContext;
    }

    /**
     * Sets the applications servlet locator instance.
     *
     * @param \TechDivision\WebContainer\ResourceLocator $servletLocator The servlet locator instance
     *
     * @return void
     */
    public function setServletLocator(ResourceLocator $servletLocator)
    {
        $this->servletLocator = $servletLocator;
    }

    /**
     * Return the servlet locator instance.
     *
     * @return \TechDivision\WebContainer\ResourceLocator The servlet locator instance
     */
    public function getServletLocator()
    {
        return $this->servletLocator;
    }

    /**
     * Locates and returns the servlet instance that handles
     * the request passed as parameter.
     * 
     * @param \TechDivision\Servlet\Http\HttpServletRequest $servletRequest The request instance
     *
     * @return \TechDivision\Servlet\Servlet The servlet instance to handle the request
     */
    public function locate(HttpServletRequest $servletRequest)
    {
        return $this->getServletLocator()->locate($servletRequest);
    }

    /**
     * Checks if the application is a virtual host for the passed server name.
     *
     * @param string $serverName The server name to check the application being a virtual host of
     *
     * @return boolean TRUE if the application is a virtual host, else FALSE
     */
    public function isVHostOf($serverName)
    {
        
        // check if the application is a virtual host for the passed server name
        foreach ($this->getVHosts() as $virtualHost) {
        
            // compare the virtual host name itself
            if (strcmp($virtualHost->getName(), $serverName) === 0) {
                return true;
            }
        }
        
        return false;
    }
}
