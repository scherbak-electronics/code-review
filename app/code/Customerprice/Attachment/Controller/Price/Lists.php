<?php 

namespace Customerprice\Attachment\Controller\Price;  

use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session;
use Magento\Framework\Controller\ResultFactory;

class Lists extends \Magento\Framework\App\Action\Action 
{ 
	protected $customerSession;
	protected $resultFactory;

    public function __construct(
        Context $context,
		Session $session,
		ResultFactory $resultFactory
    ) {
        $this->customerSession = $session;
        $this->resultFactory = $resultFactory;
        parent::__construct($context);
    }

	public function execute() 
	{ 
		if($this->customerSession->isLoggedIn()) {
			$this->_view->loadLayout(); 
			$this->_view->renderLayout(); 
		} else {
			$resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
			$resultRedirect->setPath('customer/account');
        	return $resultRedirect;
		}

	} 
} 

