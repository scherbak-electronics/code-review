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

use Webkul\ImageGallery\Controller\Adminhtml\Groups as GroupsController;
use Magento\Framework\Controller\ResultFactory;

class Edit extends GroupsController
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
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Webkul\ImageGallery\Model\GroupsFactory $groups
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $registry,
        \Webkul\ImageGallery\Model\GroupsFactory $groups
    ) {
        $this->_backendSession = $context->getSession();
        $this->_registry = $registry;
        $this->_groups = $groups;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $group = $this->_groups->create();
        if ($this->getRequest()->getParam('id')) {
            $group->load($this->getRequest()->getParam('id'));
        }
        $data = $this->_backendSession->getFormData(true);
        if (!empty($data)) {
            $group->setData($data);
        }
        $this->_registry->register('imagegallery_groups', $group);
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Webkul_ImageGallery::imagegallery');
        $resultPage->getConfig()->getTitle()->prepend(__('Group'));
        $resultPage->getConfig()->getTitle()->prepend(
            $group->getId() ? $group->getGroupCode() : __('New Group')
        );

        $block = 'Webkul\ImageGallery\Block\Adminhtml\Groups\Edit';
        $content = $resultPage->getLayout()->createBlock($block);
        $resultPage->addContent($content);
        $block = 'Webkul\ImageGallery\Block\Adminhtml\Groups\Edit\Tabs';
        $left = $resultPage->getLayout()->createBlock($block);
        $resultPage->addLeft($left);
        return $resultPage;
    }
}
