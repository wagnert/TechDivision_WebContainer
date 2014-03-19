<?php
/**
 * \TechDivision\WebContainer\ServerNodeConfiguration
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
 * @author    Johann Zelger <jz@techdivision.com>
 * @copyright 2014 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.appserver.io
 */

namespace TechDivision\WebContainer;

use TechDivision\ApplicationServer\Api\Node\NodeInterface;
use TechDivision\WebServer\Interfaces\ServerConfigurationInterface;

/**
 * Class ServerNodeConfiguration
 *
 * @category  Appserver
 * @package   TechDivision_WebContainer
 * @author    Johann Zelger <jz@techdivision.com>
 * @copyright 2014 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.appserver.io
 */
class ServerNodeConfiguration implements ServerConfigurationInterface
{

    /**
     * Hold's node instance
     *
     * @var \TechDivision\ApplicationServer\Api\Node\NodeInterface
     */
    protected $node;

    /**
     * Hold's the handlers array
     *
     * @var array
     */
    protected $handlers;

    /**
     * Hold's the connection handler array
     *
     * @var array
     */
    protected $connectionHandlers;

    /**
     * Hold's the virtual hosts array
     *
     * @var array
     */
    protected $virtualHosts;

    /**
     * Hold's the authentications array
     *
     * @var array
     */
    protected $authentications;

    /**
     * Hold's the modules array
     *
     * @var array
     */
    protected $modules;

    /**
     * Hold's the rewrites array
     *
     * @var array
     */
    protected $rewrites = array();

    /**
     * Constructs config
     *
     * @param \TechDivision\ApplicationServer\Api\Node\NodeInterface $node The node instance
     */
    public function __construct($node)
    {
        $this->node = $node;
    }

    /**
     * Return's type
     *
     * @return string
     */
    public function getType()
    {
        return $this->node->getType();
    }

    /**
     * Return's transport
     *
     * @return string
     */
    public function getTransport()
    {
        return $this->node->getParam('transport');
    }

    /**
     * Return's address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->node->getParam('address');
    }

    /**
     * Return's port
     *
     * @return int
     */
    public function getPort()
    {
        return $this->node->getParam('port');
    }

    /**
     * Return's software
     *
     * @return string
     */
    public function getSoftware()
    {
        return $this->node->getParam('software');
    }

    /**
     * Return's admin
     *
     * @return string
     */
    public function getAdmin()
    {
        return $this->node->getParam('admin');
    }

    /**
     * Return's template file path for errors page
     *
     * @return string
     */
    public function getErrorsPageTemplatePath()
    {
        return (string)$this->node->getParam('errorsPageTemplatePath');
    }

    /**
     * Return's worker number
     *
     * @return int
     */
    public function getWorkerNumber()
    {
        return $this->node->getParam('workerNumber');
    }

    /**
     * Return's context type
     *
     * @return string
     */
    public function getServerContextType()
    {
        return $this->node->getServerContext();
    }

    /**
     * Return's socket type
     *
     * @return string
     */
    public function getSocketType()
    {
        return $this->node->getSocket();
    }

    /**
     * Return's worker type
     *
     * @return string
     */
    public function getWorkerType()
    {
        return $this->node->getWorker();
    }

    /**
     * Return's document root
     *
     * @return string
     */
    public function getDocumentRoot()
    {
        return $this->node->getParam('documentRoot');
    }

    /**
     * Return's connection handlers
     *
     * @return array
     */
    public function getConnectionHandlers()
    {
        if (!$this->connectionHandlers) {
            foreach ($this->node->getConnectionHandlers() as $connectionHandler) {
                $this->connectionHandlers[$connectionHandler->getUuid()] = $connectionHandler->getType();
            }
        }

        return $this->connectionHandlers;
    }

    /**
     * Return's modules
     *
     * @return array
     */
    public function getModules()
    {
        if (!$this->modules) {
            foreach ($this->node->getModules() as $module) {
                $this->modules[$module->getUuid()] = $module->getType();
            }
        }

        return $this->modules;
    }

    /**
     * Return's handlers
     *
     * @return array
     */
    public function getHandlers()
    {
        if (!$this->handlers) {
            foreach ($this->node->getFileHandlers() as $fileHandler) {
                $this->handlers[$fileHandler->getExtension()] = $fileHandler->getName();
            }
        }

        return $this->handlers;
    }

    /**
     * Return's virtual hosts
     *
     * @return array
     */
    public function getVirtualHosts()
    {
        if (!$this->virtualHosts) {
            // iterate config
            foreach ($this->node->getVirtualHosts() as $virtualHost) {
                $virtualHostNames = explode(' ', $virtualHost->getName());
                foreach ($virtualHostNames as $virtualHostName) {
                    // set all virtual hosts params per key for faster matching later on
                    $this->virtualHosts[trim($virtualHostName)]['params'] = $virtualHost->getParamsAsArray();
                    // Also add the rewrites to the virtual host configuration
                    $this->virtualHosts[trim($virtualHostName)]['rewrites'] = $virtualHost->getRewritesAsArray();
                }
            }
        }

        return $this->virtualHosts;
    }

    /**
     * Returns the authentication configuration.
     *
     * @return array The array with the authentication configuration
     */
    public function getAuthentications()
    {
        if (!$this->authentications) {
            // iterate config
            foreach ($this->node->getAuthentications() as $authentication) {
                $authenticationUri = $authentication->getUri();
                $this->authentications[$authenticationUri] = $authentication->getParamsAsArray();
            }
        }

        return $this->authentications;
    }

    /**
     * Return's cert path
     *
     * @return string
     */
    public function getCertPath()
    {
        return $this->node->getParam('certPath');
    }

    /**
     * Return's passphrase
     *
     * @return string
     */
    public function getPassphrase()
    {
        return $this->node->getParam('passphrase');
    }

    /**
     * Returns the rewrite configuration.
     *
     * @return array
     */
    public function getRewrites()
    {
        // init rewrites
        if (!$this->rewrites) {

            $this->rewrites = array();
        }

        // prepare the array with the rewrite rules
        foreach ($this->node->getRewrites() as $rewrite) {

            // Build up the array entry
            $this->rewrites[] = array(
                'condition' => $rewrite->getCondition(),
                'target' => $rewrite->getTarget(),
                'flag' => $rewrite->getFlag()
            );
        }

        // return the rewrites
        return $this->rewrites;
    }

    /**
     * Will prepend a given array of rewrite arrays to the global rewrite pool.
     * Rewrites arrays have to be the form of array('condition' => ..., 'target' => ..., 'flag' => ...)
     *
     * @param array $rewriteArrays The array of rewrite arrays(!) to prepend
     *
     * @return boolean
     */
    public function prependRewriteArrays(array $rewriteArrays)
    {
        $this->rewrites = array_merge($rewriteArrays, $this->rewrites);

        return (bool) $this->rewrites;
    }
}
