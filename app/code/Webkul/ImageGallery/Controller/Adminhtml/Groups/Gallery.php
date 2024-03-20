<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_ImageGallery
 * @author    Webkul
 * @copyright Copyright (c) 2010-2016 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\ImageGallery\Controller\Adminhtml\Groups;

class Gallery extends \Webkul\ImageGallery\Controller\Adminhtml\Groups
{
    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $_backendSession;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    /**
     * @var \Webkul\ImageGallery\Model\GroupsFactory
     */
    protected $_groups;

    /**
     * @var \Magento\Framework\View\Result\LayoutFactory
     */
    protected $_resultLayoutFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Webkul\ImageGallery\Model\GroupsFactory $groups
     * @param \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $registry,
        \Webkul\ImageGallery\Model\GroupsFactory $groups,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
    ) {
        $this->_backendSession = $context->getSession();
        $this->_registry = $registry;
        $this->_groups = $groups;
        $this->_resultLayoutFactory = $resultLayoutFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\View\Result\Layout
     */
    public function execute()
    {
        $groups = $this->_groups->create();
        if ($this->getRequest()->getParam('id')) {
            $groups->load($this->getRequest()->getParam('id'));
        }
        $data = $this->_backendSession->getFormData(true);
        if (!empty($data)) {
            $groups->setData($data);
        }
        $this->_registry->register('imagegallery_groups', $groups);
        $resultLayout = $this->_resultLayoutFactory->create();
        $resultLayout->getLayout()
                    ->getBlock('imagegallery.groups.edit.tab.gallery');
        return $resultLayout;
    }
}
