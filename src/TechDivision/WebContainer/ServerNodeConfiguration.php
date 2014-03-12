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
        $connectionHandlers = array();
        foreach ($this->node->getConnectionHandlers() as $connectionHandler) {
            $connectionHandlers[$connectionHandler->getUuid()] = $connectionHandler->getType();
        }
        return $connectionHandlers;
    }

    /**
     * Return's modules
     *
     * @return array
     */
    public function getModules()
    {
        $modules = array();
        foreach ($this->node->getModules() as $module) {
            $modules[$module->getUuid()] = $module->getType();
        }
        return $modules;
    }

    /**
     * Return's handlers
     *
     * @return array
     */
    public function getHandlers()
    {
        $handlers = array();
        foreach ($this->node->getFileHandlers() as $fileHandler) {
            $handlers[$fileHandler->getName()] = $fileHandler->getType();
        }
        return $handlers;
    }

    /**
     * Return's virtual hosts
     *
     * @return array
     */
    public function getVirtualHosts()
    {
        // init virutalHosts
        $virtualHosts = array();
        // iterate config
        foreach ($this->node->getVirtualHosts() as $virtualHost) {
            $virtualHostNames = explode(' ' , $virtualHost->getName());
            foreach ($virtualHostNames as $virtualHostName) {
                // set all virtual hosts params per key for faster matching later on
                $virtualHosts[trim($virtualHostName)] = $virtualHost->getParamsAsArray();
            }
        }
        return $virtualHosts;
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
}
