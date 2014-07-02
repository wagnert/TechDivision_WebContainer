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

use TechDivision\Storage\StackableStorage;
use TechDivision\ApplicationServer\AbstractDeployment;
use TechDivision\WebSocketServer\HandlerManager;
use TechDivision\WebSocketServer\HandlerLocator;
use TechDivision\ServletEngine\DefaultSessionSettings;
use TechDivision\ServletEngine\PersistentSessionManager;
use TechDivision\ServletEngine\StandardSessionManager;
use TechDivision\ServletEngine\Authentication\StandardAuthenticationManager;
use TechDivision\ServletEngine\StandardSessionMarshaller;
use TechDivision\ServletEngine\SessionFactory;
use TechDivision\ServletEngine\FilesystemPersistenceManager;
use TechDivision\ServletEngine\StandardGarbageCollector;

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

        // create authentication and session manager instance
        $authenticationManager = $this->getAuthenticationManager();

        // gather all the deployed web applications
        foreach (new \FilesystemIterator($this->getWebappPath()) as $folder) {

            // check if a deployment descriptor is available
            $deploymentDescriptor = new \SplFileInfo($folder . DIRECTORY_SEPARATOR . 'WEB-INF' . DIRECTORY_SEPARATOR . 'web.xml');

            // check if file or subdirectory has been found and a deployment descriptor is available
            if ($folder->isDir() === true && $deploymentDescriptor->isFile() === true) {

                // initialize the application instance
                $application = new WebApplication();
                $application->injectAuthenticationManager($authenticationManager);
                $application->injectSessionManager($this->getSessionManager());
                $application->injectInitialContext($this->getInitialContext());
                $application->injectContainerNode($this->getContainerNode());
                $application->injectName($folder->getBasename());
                $application->injectAppBase($this->getAppBase());
                $application->injectBaseDirectory($this->getBaseDirectory());
                $application->injectServletContext($this->getServletContext($folder));
                $application->injectHandlerManager($this->getHandlerManager($folder));

                // adds the default class loader
                $application->addClassLoader($this->getInitialContext()->getClassLoader());

                // add the application to the available applications
                $this->addApplication($application);
            }
        }

        // return initialized applications
        return $this;
    }

    /**
     * Creates and returns a new servlet context that handles the servlets
     * found in the passe web application folder.
     *
     * @param \SplFileInfo $folder The folder with the web application
     *
     * @return \TechDivision\WebContainer\ServletManager The initialized servlet context
     */
    protected function getServletContext(\SplFileInfo $folder)
    {
        $servletContext = new ServletManager();
        $servletContext->injectWebappPath($folder->getPathname());
        $servletContext->injectResourceLocator($this->getResourceLocator());
        return $servletContext;
    }

    /**
     * Creates and returns a new resource locator to locate the servlet that
     * has to handle a request.
     *
     * @return \TechDivision\WebContainer\ServletLocator The resource locator instance
     */
    protected function getResourceLocator()
    {
        return new ServletLocator();
    }

    /**
     * Creates and returns a new handler manager that handles the handler
     * found in the passe web application folder.
     *
     * @param \SplFileInfo $folder The folder with the web application
     *
     * @return \TechDivision\WebSocketServer\HandlerManager The initialized handler manager
     */
    protected function getHandlerManager(\SplFileInfo $folder)
    {
        $handlerManager = new HandlerManager();
        $handlerManager->injectWebappPath($folder->getPathname());
        $handlerManager->injectHandlerLocator($this->getHandlerLocator());
        return $handlerManager;
    }

    /**
     * Creates and returns a new handler locator to locate the handler that
     * has to handle a request.
     *
     * @return \TechDivision\WebSocketServer\HandlerLocator The handler locator instance
     */
    protected function getHandlerLocator()
    {
        return new HandlerLocator();
    }

    /**
     * Returns an initialized session manager instance.
     *
     * @return \TechDivision\ServletEngine\SessionManager The session manager instance
     */
    protected function getSessionManager()
    {

        // load the system configuration
        $systemConfiguration = $this->getInitialContext()->getSystemConfiguration();

        // initialize the session pool
        $sessions = new StackableStorage();
        $checksums = new StackableStorage();
        $sessionPool = new StackableStorage();
        $sessionSettings = new DefaultSessionSettings();
        $sessionMarshaller = new StandardSessionMarshaller();

        // we need a session factory instance
        $sessionFactory = new SessionFactory($sessionPool);
        $sessionFactory->start();

        // we need a persistence manager and garbage collector
        $persistenceManager = new FilesystemPersistenceManager();
        $persistenceManager->injectSessions($sessions);
        $persistenceManager->injectChecksums($checksums);
        $persistenceManager->injectSessionSettings($sessionSettings);
        $persistenceManager->injectSessionMarshaller($sessionMarshaller);
        $persistenceManager->injectSessionFactory($sessionFactory);
        $persistenceManager->injectUser($systemConfiguration->getParam('user'));
        $persistenceManager->injectGroup($systemConfiguration->getParam('group'));
        $persistenceManager->injectUmask($systemConfiguration->getParam('umask'));
        $persistenceManager->start();

        // we need a garbage collector
        $garbageCollector = new StandardGarbageCollector();
        $garbageCollector->injectSessions($sessions);
        $garbageCollector->injectSessionSettings($sessionSettings);
        $garbageCollector->start();

        // and finally we need the session manager instance
        $sessionManager = new StandardSessionManager();
        $sessionManager->injectSessions($sessions);
        $sessionManager->injectSessionSettings($sessionSettings);
        $sessionManager->injectSessionFactory($sessionFactory);
        $sessionManager->injectPersistenceManager($persistenceManager);
        $sessionManager->injectGarbageCollector($garbageCollector);

        // return the session manager instance
        return $sessionManager;
    }

    /**
     * Returns the authentication manager.
     *
     * @return \TechDivision\ServletEngine\Authentication\AuthenticationManager
     */
    protected function getAuthenticationManager()
    {
        return new StandardAuthenticationManager();
    }

    /**
     * (non-PHPdoc)
     *
     * @return string The path to the webapps folder
     * @see ApplicationService::getWebappPath()
     */
    public function getWebappPath()
    {
        return $this->getBaseDirectory($this->getAppBase());
    }
}
