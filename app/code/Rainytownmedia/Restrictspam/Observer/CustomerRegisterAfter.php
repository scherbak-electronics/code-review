<?php

namespace Rainytownmedia\Restrictspam\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class CustomerRegisterAfter implements ObserverInterface {
	protected $_request;
	protected $_objectManager;
	protected $_logspam;
	protected $_helper;
    public function __construct(
		\Magento\Framework\App\RequestInterface $request,
		\Magento\Framework\ObjectManagerInterface $objectManager,
		\Rainytownmedia\Restrictspam\Model\LogspamFactory $logspam,
		\Rainytownmedia\Restrictspam\Helper\Data $helper
	){
		$this->_request = $request;
        $this->_objectManager = $objectManager;
		$this->_logspam = $logspam;
		$this->_helper = $helper;
    }

    public function execute(Observer $observer)
    {
		if($this->_helper->isRestrictspam('user_create')){
			$event = $observer->getEvent();
			$customer = $event->getCustomer();
			if($customer->getId()){
			$param = $this->_request->getParam('logspam');
				if( isset($param['current_time']) )
					$speed= $this->_helper->microtime_float() - $param['current_time'] ;
				else $speed = 0;
				
				$speed = round($speed* 1000); //convert to ms
				$logspam = $this->_logspam->create();
				$logspam->setCustomer_id($customer->getId())
					->setEmail($customer->getEmail())
					->setForm_type('customer_register')
					->setSpeed($speed)
					->setIs_spam(0)
					->save();
			}
		}
	}
}