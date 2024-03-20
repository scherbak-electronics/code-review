<?php
namespace Lumenstarled\Override\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer as EventObserver;
class updateCustomFieldsToQuote implements ObserverInterface {
	public function __construct(
		\Magento\Framework\App\Request\Http $request
	){
		$this->request = $request;
	}
	
	public function execute(EventObserver $observer){
		$company = $this->request->getParam('company_cus');
		$quotation = $observer->getEvent()->getQuote();
		$quotation->setCompany($company)->save();
    }
	
	public function addlog($data, $name){
		$writer = new \Zend\Log\Writer\Stream(BP . "/var/log/{$name}");
		$logger = new \Zend\Log\Logger();
		$logger->addWriter($writer);
		$logger->info($data);
	}
	
}