<?php
/**
 * Copyright (c) 2019. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\DeskEmail\Model\Container;

use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\Store;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class Container
 * @package Cart2Quote\DeskEmail\Model\Container
 */
abstract class Container implements IdentityInterface
{
    /**
     * Store Manager
     *
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Desk store config
     *
     * @var ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * Store
     *
     * @var Store
     */
    protected $_store;

    /**
     * Main name for emailing
     *
     * @var string
     */
    protected $mainName;

    /**
     * Main email for emailing
     *
     * @var string
     */
    protected $mainEmail;

    /**
     * Class Container constructor
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->_storeManager = $storeManager;
    }

    /**
     * Return store configuration value
     *
     * @param string $path
     * @param int $storeId
     * @return mixed
     */
    protected function getConfigValue($path, $storeId)
    {
        return $this->_scopeConfig->getValue(
            $path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Set current store
     *
     * @param Store $_store
     * @return void
     */
    public function setStore(Store $_store)
    {
        $this->_store = $_store;
    }

    /**
     * Return store
     *
     * @return Store
     */
    public function getStore()
    {
        //current store
        if ($this->_store instanceof Store) {
            return $this->_store;
        }
        return $this->_storeManager->getStore();
    }

    /**
     * Set main name
     *
     * @param string $name
     * @return void
     */
    public function setMainName($name)
    {
        $this->mainName = $name;
    }

    /**
     * Set main email
     *
     * @param string $email
     * @return void
     */
    public function setMainEmail($email)
    {
        $this->mainEmail = $email;
    }

    /**
     * Return main name
     *
     * @return string
     */
    public function getMainName()
    {
        return $this->mainName;
    }

    /**
     * Return main email
     *
     * @return string
     */
    public function getMainEmail()
    {
        return $this->mainEmail;
    }
}
