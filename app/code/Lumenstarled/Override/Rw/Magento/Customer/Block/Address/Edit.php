<?php
namespace Lumenstarled\Override\Rw\Magento\Customer\Block\Address;

use Magento\Customer\Api\AddressMetadataInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\NoSuchEntityException;

class Edit extends \Magento\Customer\Block\Address\Edit {
	/* protected $addrTypeParam = false;
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Directory\Helper\Data $directoryHelper,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\App\Cache\Type\Config $configCacheType,
        \Magento\Directory\Model\ResourceModel\Region\CollectionFactory $regionCollectionFactory,
        \Magento\Directory\Model\ResourceModel\Country\CollectionFactory $countryCollectionFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Api\AddressRepositoryInterface $addressRepository,
        \Magento\Customer\Api\Data\AddressInterfaceFactory $addressDataFactory,
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
        \Magento\Framework\Api\DataObjectHelper $dataObjectHelper,
        array $data = [],
        AddressMetadataInterface $addressMetadata = null
    ) {
        parent::__construct(
			$context,
			$directoryHelper,
			$jsonEncoder,
			$configCacheType,
			$regionCollectionFactory,
			$countryCollectionFactory,
			$customerSession,
			$addressRepository,
			$addressDataFactory,
			$currentCustomer,
			$dataObjectHelper,
			$data,
			$addressMetadata
        );
		
		$this->addrTypeParam = $this->getTypeParam();
    } */
	public function getTitle()
    {
		if( 
			$this->getTypeParam() &&
			$this->getAddress()->getId()
		){
			if( $this->getTypeParam() == 'billing' ){
				$title = __('Edit Billing Address');
			}else{
				$title = __('Edit Shipping Address');
			}
			return $title;
		}
		return parent::getTitle();
    }
	
	public function getSaveUrl(){
		if( $this->needCreateNew() ){
			return $this->_urlBuilder->getUrl(
				'customer/address/formPost',
				['_secure' => true, 'id' => $this->getAddress()->getId(), 'create_new' => $this->getTypeParam()]
			);
		}
		
		return parent::getSaveUrl();
    }
	
	public function getTypeParam(){
		$address_type = $this->getRequest()->getParam('address_type');
		if( in_array($address_type, ['billing', 'shipping']) ) return $address_type;
		return false;
	}
	public function needCreateNew(){
		if( 
			$this->getTypeParam() &&
			$this->isDefaultBilling() &&
			$this->isDefaultShipping() &&
			$this->getAddress()->getId()
		){
			return true;
		}
		
		return false;
	}
}