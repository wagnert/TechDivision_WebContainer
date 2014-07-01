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

use TechDivision\Storage\StackableStorage;
use TechDivision\Servlet\Servlet;
use TechDivision\Servlet\ServletContext;
use TechDivision\Servlet\Http\HttpServletRequest;
use TechDivision\ServletEngine\Http\RequestContext;
use TechDivision\PBC\AutoLoader;
use TechDivision\PBC\Config;
use TechDivision\ApplicationServer\Interfaces\ApplicationInterface;
use TechDivision\ApplicationServer\Api\ContainerService;
use TechDivision\ApplicationServer\Api\Node\AppNode;
use TechDivision\ApplicationServer\Api\Node\NodeInterface;
use TechDivision\WebSocketServer\HandlerManager;
use TechDivision\WebSocketServer\ResourceLocatorInterface;
use TechDivision\WebSocketProtocol\Request;

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
class WebApplication extends \Thread implements ApplicationInterface, RequestContext
{

    /**
     * The app node the application is belonging to.
     *
     * @var \TechDivision\ApplicationServer\Api\Node\AppNode
     */
    protected $appNode;

    /**
     * The applications base directory.
     *
     * @var string
     */
    protected $appBase;

    /**
     * The web containers base directory.
     *
     * @var string
     */
    protected $baseDirectory;

    /**
     * The app node the application is belonging to.
     *
     * @var \TechDivision\ApplicationServer\Api\Node\ContainerNode
     */
    protected $containerNode;

    /**
     * The unique application name.
     *
     * @var string
     */
    protected $name;

    /**
     * Array with available VHost configurations.
     *
     * @var array
     */
    protected $vhosts;

    /**
     * The host configuration.
     *
     * @var \TechDivision\ApplicationServer\Configuration
     */
    protected $configuration;

    /**
     * The initial context instance.
     *
     * @var \TechDivision\ApplicationServer\InitialContext
     */
    protected $initialContext;

    /**
     * The session manager that is bound to the request.
     *
     * @var \TechDivision\ServletEngine\SessionManager
     */
    protected $sessionManager;

    /**
     * The authentication manager that is bound to the request.
     *
     * @var \TechDivision\ServletEngine\AuthenticationManager
     */
    protected $authenticationManager;

    /**
     * The servlet context that handles the servlets of this application.
     *
     * @var \TechDivision\Servlet\ServletContext
     */
    protected $servletContext;

    /**
     * The handler manager that handles the handlers of this application.
     *
     * @var \TechDivision\WebSocketServer\HandlerManager
     */
    protected $handlerManager;

    /**
     * Initializes the application context.
     */
    public function __construct()
    {
        $this->vhosts = new StackableStorage();
    }

    /**
     * Returns a attribute from the application context.
     *
     * @param string $name the name of the attribute to return
     *
     * @throws \Exception
     * @return void
     */
    public function getAttribute($name)
    {
        throw new \Exception(__METHOD__ . ' not implemented yet');
    }

    /**
     * The initial context instance.
     *
     * @param \TechDivision\ApplicationServer\InitialContext $initialContext The initial context instance
     *
     * @return void
     */
    public function injectInitialContext($initialContext)
    {
        $this->initialContext = $initialContext;
    }

    /**
     * Injects the application name.
     *
     * @param string $name The application name
     *
     * @return void
     */
    public function injectName($name)
    {
        $this->name = $name;
    }

    /**
     * Injects the applications base directory.
     *
     * @param string $appBase The applications base directory
     *
     * @return void
     */
    public function injectAppBase($appBase)
    {
        $this->appBase = $appBase;
    }

    /**
     * Injects the containers base directory.
     *
     * @param string $baseDirectory The web containers base directory
     *
     * @return void
     */
    public function injectBaseDirectory($baseDirectory)
    {
        $this->baseDirectory = $baseDirectory;
    }

    /**
     * Injects the applications servlet context instance.
     *
     * @param \TechDivision\Servlet\ServletContext $servletContext The servlet context instance
     *
     * @return void
     */
    public function injectServletContext(ServletContext $servletContext)
    {
        $this->servletContext = $servletContext;
    }

    /**
     * Injects the applications handler manager instance.
     *
     * @param \TechDivision\WebSocketServer\HandlerManager $handlerManager The handler manager instance
     *
     * @return void
     */
    public function injectHandlerManager(HandlerManager $handlerManager)
    {
        $this->handlerManager = $handlerManager;
    }

    /**
     * Injects the session manager that is bound to the request.
     *
     * @param \TechDivision\ServletEngine\SessionManager $sessionManager The session manager to bound this request to
     *
     * @return void
     */
    public function injectSessionManager($sessionManager)
    {
        $this->sessionManager = $sessionManager;
    }

    /**
     * Injects the authentication manager that is bound to the request.
     *
     * @param \TechDivision\ServletEngine\AuthenticationManager $authenticationManager The authentication manager to bound this request to
     *
     * @return void
     */
    public function injectAuthenticationManager($authenticationManager)
    {
        $this->authenticationManager = $authenticationManager;
    }

    /**
     * Injects the container node the application is belonging to
     *
     * @param \TechDivision\ApplicationServer\Api\Node\ContainerNode $containerNode The container node the application is belonging to
     *
     * @return void
     */
    public function injectContainerNode($containerNode)
    {
        $this->containerNode = $containerNode;
    }

    /**
     * Returns the session manager instance associated with this request.
     *
     * @return \TechDivision\ServletEngine\SessionManager The session manager instance
     */
    public function getSessionManager()
    {
        return $this->sessionManager;
    }

    /**
     * Returns the authentication manager instance associated with this request.
     *
     * @return \TechDivision\ServletEngine\AuthenticationManager The authentication manager instance
     */
    public function getAuthenticationManager()
    {
        return $this->authenticationManager;
    }

    /**
     * Set's the app node the application is belonging to
     *
     * @param AppNode $appNode The app node the application is belonging to
     *
     * @return void
     */
    public function setAppNode($appNode)
    {
        $this->appNode = $appNode;
    }

    /**
     * Return's the app node the application is belonging to.
     *
     * @return AppNode The app node the application is belonging to
     */
    public function getAppNode()
    {
        return $this->appNode;
    }

    /**
     * Return's the app node the application is belonging to.
     *
     * @return ContainerNode The app node the application is belonging to
     */
    public function getContainerNode()
    {
        return $this->containerNode;
    }

    /**
     * Returns the application name (that has to be the class namespace, e.g. TechDivision\Example)
     *
     * @return string The application name
     */
    public function getName()
    {
        return $this->name;
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
     * Return the handler manager instance.
     *
     * @return \TechDivision\WebSocketServer\HandlerManager The handler manager instance
     */
    public function getHandlerManager()
    {
        return $this->handlerManager;
    }

    /**
     * (non-PHPdoc)
     *
     * @param string $directoryToAppend The directory to append to the base directory
     *
     * @return string The base directory with appended dir if given
     */
    public function getBaseDirectory($directoryToAppend = null)
    {
        $baseDirectory = $this->baseDirectory;
        if ($directoryToAppend != null) {
            $baseDirectory .= $directoryToAppend;
        }
        return $baseDirectory;
    }

    /**
     * (non-PHPdoc)
     *
     * @return string The path to the webapps folder
     * @see ApplicationService::getWebappPath()
     */
    public function getWebappPath()
    {
        return $this->getBaseDirectory($this->getAppBase() . DIRECTORY_SEPARATOR . $this->getName());
    }

    /**
     * (non-PHPdoc)
     *
     * @return string The app base
     * @see ContainerService::getAppBase()
     */
    public function getAppBase()
    {
        return $this->appBase;
    }

    /**
     * (non-PHPdoc)
     *
     * @param string $className The fully qualified class name to return the instance for
     * @param array  $args      Arguments to pass to the constructor of the instance
     *
     * @return object The instance itself
     * @see InitialContext::newInstance()
     */
    public function newInstance($className, array $args = array())
    {
        return $this->getInitialContext()->newInstance($className, $args);
    }

    /**
     * (non-PHPdoc)
     *
     * @param string $className The API service class name to return the instance for
     *
     * @return ServiceInterface The service instance
     * @see InitialContext::newService()
     */
    public function newService($className)
    {
        return $this->getInitialContext()->newService($className);
    }

    /**
     * Returns the initial context instance.
     *
     * @return InitialContext The initial Context
     */
    public function getInitialContext()
    {
        return $this->initialContext;
    }

    /**
     * Return's the applications available VHost configurations.
     *
     * @return array The available VHost configurations
     */
    public function getVhosts()
    {
        return $this->vhosts;
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

        return false;

        // check if the application is a virtual host for the passed server name
        foreach ($this->getVHosts() as $virtualHost) {

            // compare the virtual host name itself
            if (strcmp($virtualHost->getName(), $serverName) === 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * (non-PHPdoc)
     *
     * @return AppNode The node representation of the application
     * @see ApplicationInterface::newAppNode()
     */
    public function newAppNode()
    {
        // create a new AppNode and initialize it with the values from this instance
        $appNode = new AppNode();
        $appNode->setNodeName('application');
        $appNode->setName($this->getName());
        $appNode->setWebappPath($this->getWebappPath());
        $appNode->setParentUuid($this->getContainerNode()->getParentUuid());
        $appNode->setUuid($appNode->newUuid());

        // set the AppNode in the instance itself
        $this->setAppNode($appNode);

        // return the AppNode instance
        return $appNode;
    }

    /**
     * Bounds the application to the passed virtual host.
     *
     * @param \TechDivision\WebContainer\VirtualHost $virtualHost The virtual host to add
     *
     * @return void
     */
    protected function addVirtualHost(VirtualHost $virtualHost)
    {
        $this->vhosts[] = $virtualHost;
    }

    /**
     * Has been automatically invoked by the container after the application
     * instance has been created.
     *
     * @return \TechDivision\ApplicationServer\Interfaces\ApplicationInterface The connected application
     */
    public function connect()
    {
        // start the application instance
        $this->start();

        // return the instance itself
        return $this;
    }

    public function run()
    {

        // initialize the class loader with the additional folders
        set_include_path(get_include_path() . PATH_SEPARATOR . $this->getWebappPath());
        set_include_path(get_include_path() . PATH_SEPARATOR . $this->getWebappPath() . DIRECTORY_SEPARATOR . 'WEB-INF' . DIRECTORY_SEPARATOR . 'classes');
        set_include_path(get_include_path() . PATH_SEPARATOR . $this->getWebappPath() . DIRECTORY_SEPARATOR . 'WEB-INF' . DIRECTORY_SEPARATOR . 'lib');

        // register the class loader again, because in a Thread the context has been lost maybe
        $this->getInitialContext()->getClassLoader()->register(true);

        /**
         * @TODO Refactor PBC!
         *
         * $config Config::getInstance();
         * $config->setXXX();
         *
         * $classLoader = new AutoLoader($config);
         *
         * $autoLoaderConfig = $this->getWebappPath() . DIRECTORY_SEPARATOR . 'WEB-INF' . DIRECTORY_SEPARATOR . 'pbc.conf.json';
         * if (file_exists($autoLoaderConfig)) {
         *     Config::getInstance()->load($autoLoaderConfig);
         *     $classLoader = new AutoLoader();
         *     $classLoader->register();
         * }
         */

        // load and initialize the servlets
        if ($this->servletContext) {
            $this->servletContext->initialize();
        }

        // load and initialize the handlers
        if ($this->handlerManager) {
            $this->handlerManager->initialize();
        }

        // load and initialize the session manager
        if ($this->sessionManager) {

            // prepare the default session save path
            $sessionSavePath = $this->getWebappPath() . DIRECTORY_SEPARATOR . 'WEB-INF' . DIRECTORY_SEPARATOR . 'sessions';

            // load the settings, set the default session save path
            $sessionSettings = $this->sessionManager->getSessionSettings();
            $sessionSettings->setSessionSavePath($sessionSavePath);

            // if we've session parameters defined in our servlet context
            if ($this->servletContext && $this->servletContext->hasSessionParameters()) {

                // we want to merge the session settings from the servlet context into our session manager
                $sessionSettings->mergeServletContext($this->servletContext);
            }

            // initialize the session manager
            $this->sessionManager->initialize();
        }

        /*
         * Initialize the flag to lock/unlock application thread.
         *
         * If the application should handle the request, the flag
         * has to be set to TRUE (from outside) and the application
         * notified with the notify() method.
         */
        $this->handleRequest = false;

        while (true) {  // run and wait to handle applications

            // we need to be in a synchronized environment
            $this->synchronized(function ($thread) {

                // wait if should NOT handle a new request
                if ($thread->handleRequest === false) {
                    $thread->wait();
                }

                // reset request/response instance
                $servletRequest = $this->servletRequest;
                $servletResponse = $this->servletResponse;

                // locate and service the servlet
                $servlet = $this->servletContext->locate($servletRequest);
                $servlet->service($servletRequest, $servletResponse);

                // notify ourself, because we're waiting in the synchronized() method outside
                $this->notify();

                // we've finished working on this request
                $this->handleRequest = false;

            }, $this);
        }
    }
}
