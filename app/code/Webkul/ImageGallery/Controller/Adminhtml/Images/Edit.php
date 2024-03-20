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
namespace Webkul\ImageGallery\Controller\Adminhtml\Images;

use Webkul\ImageGallery\Controller\Adminhtml\Images as ImagesController;
use Magento\Framework\Controller\ResultFactory;

class Edit extends ImagesController
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
     * @var \Webkul\ImageGallery\Model\ImagesFactory
     */
    protected $_images;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Webkul\ImageGallery\Model\ImagesFactory $images
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $registry,
        \Webkul\ImageGallery\Model\ImagesFactory $images
    ) {
        $this->_backendSession = $context->getSession();
        $this->_registry = $registry;
        $this->_images = $images;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $images = $this->_images->create();
        if ($this->getRequest()->getParam('id')) {
            $images->load($this->getRequest()->getParam('id'));
        }
        $data = $this->_backendSession->getFormData(true);
        if (!empty($data)) {
            $images->setData($data);
        }
        $this->_registry->register('imagegallery_images', $images);
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Webkul_ImageGallery::imagegallery');
        $resultPage->getConfig()->getTitle()->prepend(__('Images'));
        $resultPage->getConfig()->getTitle()->prepend(
            $images->getId() ? $images->getTitle() : __('New Image')
        );
        $block = 'Webkul\ImageGallery\Block\Adminhtml\Images\Edit';
        $content = $resultPage->getLayout()->createBlock($block);
        $resultPage->addContent($content);
        return $resultPage;
    }
}
