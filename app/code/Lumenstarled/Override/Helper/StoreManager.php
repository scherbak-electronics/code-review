<?php
namespace Lumenstarled\Override\Helper;
use \Magento\Framework\App\Helper\AbstractHelper;

class StoreManager extends AbstractHelper{
	protected $_storeManager;   
	public function __construct(
		\Magento\Store\Model\StoreManagerInterface $storeManager  
    ){        
        $this->_storeManager = $storeManager;        
    }
	
	public function getStoreId(){
        return $this->_storeManager->getStore()->getId();
    }
    
    public function getWebsiteId(){
        return $this->_storeManager->getStore()->getWebsiteId();
    }
    
    public function getStoreCode(){
        return $this->_storeManager->getStore()->getCode();
    }
   
    public function getStoreName(){
        return $this->_storeManager->getStore()->getName();
    }
    
    public function getStoreUrl($fromStore = true){
        return $this->_storeManager->getStore()->getCurrentUrl($fromStore);
    }
   
    public function isStoreActive(){
        return $this->_storeManager->getStore()->isActive();
    }
	
	public function getUrl($storeId, $url, $params){
		return $this->_storeManager->getStore($storeId)->getUrl($url, $params);
	}
}