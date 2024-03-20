<?php
namespace Lumenstarled\Override\Plugin\Magento\Customer\Block\Account\Dashboard;
class Address{
	public function afterGetPrimaryShippingAddressEditUrl(
		\Magento\Customer\Block\Account\Dashboard\Address $subject,
		$result
	){
		if($result){
			if( substr($result, -1) != '/' ) $result .= '/';
			$result .= 'address_type/shipping';
		}
		return $result;
	}
	
	public function afterGetPrimaryBillingAddressEditUrl(
		\Magento\Customer\Block\Account\Dashboard\Address $subject,
		$result
	){
		if($result){
			if( substr($result, -1) != '/' ) $result .= '/';
			$result .= 'address_type/billing';
		}
		return $result;
	}
}