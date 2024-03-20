<?php

namespace Rainytownmedia\Restrictspam\Plugin\Customer\Controller\Account;

use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\UrlFactory;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

class ForgotPasswordPost
{
    protected $urlModel;
    protected $resultRedirectFactory;
    protected $messageManager;
	protected $_dateTime;
	protected $_logspam;
	protected $_helper;
	protected $escaper;
    public function __construct(
        UrlFactory $urlFactory,
        RedirectFactory $redirectFactory,
        ManagerInterface $messageManager,
		ScopeConfigInterface $scopeConfig,
		\Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
		\Rainytownmedia\Restrictspam\Model\LogspamFactory $logspam,
		\Rainytownmedia\Restrictspam\Helper\Data $helper,
		\Magento\Framework\Escaper $escaper

    ){
        $this->urlModel = $urlFactory->create();
        $this->resultRedirectFactory = $redirectFactory;
        $this->messageManager = $messageManager;
		$this->scopeConfig = $scopeConfig;
		$this->_dateTime = $dateTime;
		$this->_logspam = $logspam;
		$this->_helper = $helper;
		$this->escaper = $escaper;
    }

    public function aroundExecute(
        \Magento\Customer\Controller\Account\ForgotPasswordPost $subject,
        \Closure $proceed
    ){
		$form_name = 'user_forgotpassword';
		if($this->_helper->isRestrictspam($form_name)){
			$param = $subject->getRequest()->getParam('logspam');
			
			if( isset($param['current_time']) )
				$speed= $this->_helper->microtime_float() - $param['current_time']; //convert to ms
			else $speed = 0;
			
			$speed = round($speed* 1000); //convert to ms
			
			if( isset($param['detect']) || $speed <= $this->_helper::SPEED_ALLOW ){
				/* $logspam = $this->_logspam->create();
				$logspam->setSpeed($speed)
					->setIs_spam(1)
					->setForm_type($form_name)
					->setEmail( $subject->getRequest()->getParam('email') )
					->setFirstname( $subject->getRequest()->getParam('firstname') )
					->setLastname( $subject->getRequest()->getParam('lastname') )
					->save(); */
				
				$email = (string)$subject->getRequest()->getPost('email');
				$message = __('If there is an account associated with %1 you will receive an email with a link to reset your password.', $this->escaper->escapeHtml($email) );
				$this->messageManager->addSuccessMessage($message.' . Its spam');
				$resultRedirect = $this->resultRedirectFactory->create();
				return $resultRedirect->setPath('*/*/');
			}
		}
        return $proceed();
    }
}