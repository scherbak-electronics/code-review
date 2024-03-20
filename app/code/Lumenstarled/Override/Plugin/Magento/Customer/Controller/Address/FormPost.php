<?php
namespace Lumenstarled\Override\Plugin\Magento\Customer\Controller\Address;

class FormPost{
	public function beforeExecute(
		\Magento\Customer\Controller\Address\FormPost $subject
	){
		$createNew = $subject->getRequest()->getParam('create_new');
		if( 
			in_array($createNew, ['billing', 'shipping']) &&
			$subject->getRequest()->getParam('id')
		){
			$subject->getRequest()->setParam('id', null);
			$subject->getRequest()->setParam('default_'. $createNew, 1);
			$subject->getRequest()->setPostValue('default_'. $createNew, 1);
		}
	}
}