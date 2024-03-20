<?php
/**
 * Elavon Module Helper.
 *
 * @category  Payment Integration
 * @package   Rootways_Elavon
 * @author    Developer RootwaysInc <developer@rootways.com>
 * @copyright 2017 Rootways Inc. (https://www.rootways.com)
 * @license   Rootways Custom License
 * @link      https://www.rootways.com/shop/media/extension_doc/license_agreement.pdf
 */
namespace Rootways\Elavon\Helper;

use Magento\Payment\Model\Config as PaymentConfig;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;
    
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;
    
    /**
     * @var \Magento\Framework\Encryption\EncryptorInterface
     */
    protected $_encryptor;
    
    /**
     * @var \Magento\Config\Model\ResourceModel\Config
     */
    protected $resourceConfig;
    
    /**
     * @var \Magento\Directory\Model\RegionFactory
     */
    protected $regionFactory;
    
    /**
     * @var \Magento\Directory\Model\CountryFactory
     */
    protected $countryFactory;
    
    /**
     * Helper Data.
     * @param Magento\Framework\App\Helper\Context $context
     * @param Magento\Framework\ObjectManagerInterface $objectManager
     * @param Magento\Store\Model\StoreManagerInterface $storeManager
     * @param Magento\Framework\Encryption\EncryptorInterface $encryptor
     * @param Magento\Config\Model\ResourceModel\Config $resourceConfig
     * @param Magento\Directory\Model\RegionFactory $regionFactory
     * @param Magento\Directory\Model\CountryFactory $countryFactory
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor,
        \Magento\Config\Model\ResourceModel\Config $resourceConfig,
        \Magento\Directory\Model\RegionFactory $regionFactory,
        \Magento\Directory\Model\CountryFactory $countryFactory
    ) {
        $this->_objectManager = $objectManager;
        $this->_storeManager = $storeManager;
        $this->_encryptor = $encryptor;
        $this->_customresourceConfig = $resourceConfig;
        $this->_regionFactory = $regionFactory;
        $this->_countryFactory = $countryFactory;
        parent::__construct($context);
    }
    
    /**
     * Get Configuration value from admin.
     */
    public function getConfig($config_path)
    {
        return $this->scopeConfig->getValue(
            $config_path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    
    /**
     * Get value of Merchang ID.
    */
    public function getMerchantId()
    {
        return $this->getConfig('payment/rootways_elavon_option/merchant_id');
    }
    
    /**
     * Get value of User ID.
     */
    public function getUserId()
    {
        $user_id = $this->getConfig('payment/rootways_elavon_option/user_id');
        return $this->_encryptor->decrypt($user_id);
    }
    
    /**
     * Get value of Elavon Pin.
     */
    public function getElavonPin()
    {
        $elavon_pin = $this->getConfig('payment/rootways_elavon_option/elavon_pin');
        return $this->_encryptor->decrypt($elavon_pin);
    }
    
    /**
     * Get value of Payment Mode.
     */
    public function getPaymentMode()
    {
        $payment_mode = $this->getConfig('payment/rootways_elavon_option/payment_mode');
        if ($payment_mode == 1) {
            $pMode = 'true'; // For Test Mode.
        } else {
            $pMode = 'false'; // For Live Mode.
        }
        return $pMode;
    }
    
    /**
     * Get value of Debug Setting.
    */
    public function isDebugEnabled()
    {
        return $this->getConfig('payment/rootways_elavon_option/debug');
    }
    
    /**
     * Get value of licence key from admin
     */
    public function licencekey()
    {
        return $this->getConfig('rootways_elavon/general/licencekey');
    }
    
    /**
     * Get value of secure URL from admin
     */
    public function surl()
    {
        return "aHR0cHM6Ly93d3cucm9vdHdheXMuY29tL20ydmVyaWZ5bGljLnBocA==";
    }
    
    /**
     * Get value of licence key from admin
     */
    public function act()
    {
        $dt_db_blank = $this->getConfig('rootways_elavon/general/lcstatus');
        if ($dt_db_blank == '') {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $isMultiStore =  $this->getConfig('rootways_elavon/general/ismultistore');
            $u = $this->_storeManager->getStore()->getBaseUrl();
            if ($isMultiStore == 1)  {
                $u = $objectManager->create('Magento\Backend\Helper\Data')->getHomePageUrl();
            }
            $l = $this->getConfig('rootways_elavon/general/licencekey');
            $surl = base64_decode($this->surl());
            $url = $surl."?u=".$u."&l=".$l."&extname=m2_elavon";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_URL, $url);
            $result=curl_exec($ch);
            curl_close($ch);
            $act_data = json_decode($result, true);
            if ($act_data['status'] == '0') {
                return "SXNzdWUgd2l0aCB5b3VyIFJvb3R3YXlzIGV4dGVuc2lvbiBsaWNlbnNlIGtleSwgcGxlYXNlIGNvbnRhY3QgPGEgaHJlZj0ibWFpbHRvOmhlbHBAcm9vdHdheXMuY29tIj5oZWxwQHJvb3R3YXlzLmNvbTwvYT4=";
            } else {
                $this->_customresourceConfig->saveConfig('rootways_elavon/general/lcstatus', $l, 'default', 0);
            }
        }
    }
    
    /**
     * Get value of Post URL.
     */
    public function getPostUrl()
    {
        $payment_mode = $this->getConfig('payment/rootways_elavon_option/payment_mode');
        if ($payment_mode == 1) {
            $postURL = "https://api.demo.convergepay.com/VirtualMerchantDemo/processxml.do";
        } else {
            $postURL = "https://www.convergepay.com/VirtualMerchant/processxml.do";
            //$postURL = "https://api.convergepay.com/VirtualMerchant/processxml.do"; // Old URL of Production.
        }
        return $postURL;
    }
    
    /**
     * Get value of Region Code.
     */
    public function getRegionCode($shipperRegionId)
    {
        $shipperRegion = $this->_regionFactory->create()->load($shipperRegionId);
        return $shipperRegion->getCode();
    }
    
    /**
     * Get value of Country ID.
     */
    public function getCountryName($countryCode)
    {
        $country = $this->_countryFactory->create()->loadByCode($countryCode);
        return $country->getName();
    }
}
