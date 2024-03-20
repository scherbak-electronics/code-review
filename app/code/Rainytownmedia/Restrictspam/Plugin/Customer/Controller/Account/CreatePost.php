<?php

namespace Rainytownmedia\Restrictspam\Plugin\Customer\Controller\Account;

use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\UrlFactory;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

class CreatePost
{
    protected $urlModel;
    protected $resultRedirectFactory;
    protected $messageManager;
	protected $_dateTime;
	protected $_logspam;
	protected $_helper;
    public function __construct(
        UrlFactory $urlFactory,
        RedirectFactory $redirectFactory,
        ManagerInterface $messageManager,
		ScopeConfigInterface $scopeConfig,
		\Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
		\Rainytownmedia\Restrictspam\Model\LogspamFactory $logspam,
		\Rainytownmedia\Restrictspam\Helper\Data $helper

    ){
        $this->urlModel = $urlFactory->create();
        $this->resultRedirectFactory = $redirectFactory;
        $this->messageManager = $messageManager;
		$this->scopeConfig = $scopeConfig;
		$this->_dateTime = $dateTime;
		$this->_logspam = $logspam;
		$this->_helper = $helper;
		
    }

    public function aroundExecute(
        \Magento\Customer\Controller\Account\CreatePost $subject,
        \Closure $proceed
    ){
		if($this->_helper->isRestrictspam('user_create')){
			$param = $subject->getRequest()->getParam('logspam');
			
			if( isset($param['current_time']) )
				$speed= $this->_helper->microtime_float() - $param['current_time']; //convert to ms
			else $speed = 0;
			
			$speed = round($speed* 1000); //convert to ms
			
			if( isset($param['detect']) || $speed <= $this->_helper::SPEED_ALLOW ){
				$logspam = $this->_logspam->create();
				$logspam->setSpeed($speed)
					->setIs_spam(1)
					->setForm_type('customer_register')
					->setEmail( $subject->getRequest()->getParam('email') )
					->setFirstname( $subject->getRequest()->getParam('firstname') )
					->setLastname( $subject->getRequest()->getParam('lastname') )
					->save();
				
				$url = $this->urlModel->getUrl('*/*/index', ['_secure' => true]);
				$message = 'Thank you register customer.';
				$this->messageManager->addSuccess($message);
				$resultRedirect = $this->resultRedirectFactory->create();
				return $resultRedirect->setUrl($url);
			}
		}
        return $proceed();
    }
}