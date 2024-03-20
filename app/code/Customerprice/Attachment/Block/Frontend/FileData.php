<?php
namespace Customerprice\Attachment\Block\Frontend;

use \Magento\Framework\View\Element\Template;
use \Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\StoreManagerInterface;
use Customerprice\Attachment\Model\ResourceModel\Grid\CollectionFactory;
use \Magento\Customer\Model\Session;

class FileData extends Template
{
	protected $_storeManager;
	protected $scopeConfig;
	protected $collectionFactory;
	protected $_customerSession;
	protected $groupRepository;
	const XML_PATH_MASTER_TYPE = 'pricetype/general/master_type';
	const XML_PATH_INDIVIDUAL_TYPE = 'pricetype/general/individual_type';

	public function __construct(
		Context $context,
		StoreManagerInterface $storeManager,
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
		CollectionFactory $collectionFactory,
		Session $customerSession,
		\Magento\Customer\Api\GroupRepositoryInterface $groupRepository,
		array $data = []
	)
	{
		$this->_storeManager = $storeManager;
		$this->scopeConfig = $scopeConfig;
		$this->_collectionFactory = $collectionFactory;
		$this->_customerSession = $customerSession;
		$this->groupRepository = $groupRepository;
		parent::__construct($context, $data);
	}

	public function getGroupId()
    {
    	$groupId = $this->_customerSession->getCustomerGroupId();
    	return $groupId;
	}

	public function getGroupName($groupId)
	{
	    $groupCode = $this->groupRepository->getById($groupId);
	    return $groupCode->getCode();
	}

	public function getAllFileCollection($groupCode)
	{
    	$collection = $this->_collectionFactory->create()->addFieldToFilter('customer_type', $groupCode);;
    	return $collection->getData();
	}

	public function getMasterFileCollection($groupCode)
	{
    	$PricelistType = 'Master Price List';
    	$collection = $this->_collectionFactory->create()->addFieldToFilter('customer_type', $groupCode)->addFieldToFilter('pricelist_type', $PricelistType);
    	return $collection->getData();
	}

	public function getIndividualFileCollection($groupCode)
	{
    	$PricelistType = 'Individual Price List';
    	$collection = $this->_collectionFactory->create()->addFieldToFilter('customer_type', $groupCode)->addFieldToFilter('pricelist_type', $PricelistType);
    	return $collection->getData();
	}

	public function getBaseUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl();
    }

    public function getMasterValue()
    {
    	$storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::XML_PATH_MASTER_TYPE, $storeScope);
    }

    public function getIndividualValue()
    {
    	$storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::XML_PATH_INDIVIDUAL_TYPE, $storeScope);
    }
}



