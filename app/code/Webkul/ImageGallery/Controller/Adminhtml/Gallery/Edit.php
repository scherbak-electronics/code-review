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
namespace Webkul\ImageGallery\Controller\Adminhtml\Gallery;

use Webkul\ImageGallery\Controller\Adminhtml\Gallery as GalleryController;
use Magento\Framework\Controller\ResultFactory;

class Edit extends GalleryController
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
     * @var \Webkul\ImageGallery\Model\GalleryFactory
     */
    protected $_gallery;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Webkul\ImageGallery\Model\GalleryFactory $gallery
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $registry,
        \Webkul\ImageGallery\Model\GalleryFactory $gallery
    ) {
        $this->_backendSession = $context->getSession();
        $this->_registry = $registry;
        $this->_gallery = $gallery;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $gallery = $this->_gallery->create();
        if ($this->getRequest()->getParam('id')) {
            $gallery->load($this->getRequest()->getParam('id'));
        }
        $data = $this->_backendSession->getFormData(true);
        if (!empty($data)) {
            $gallery->setData($data);
        }
        $this->_registry->register('imagegallery_gallery', $gallery);
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Webkul_ImageGallery::imagegallery');
        $resultPage->getConfig()->getTitle()->prepend(__('Gallery'));
        $title = 'New Gallery';
        $title = $gallery->getId() ? $gallery->getGalleryTitle() : __($title);
        $resultPage->getConfig()->getTitle()->prepend($title);
        $block = 'Webkul\ImageGallery\Block\Adminhtml\Gallery\Edit';
        $content = $resultPage->getLayout()->createBlock($block);
        $resultPage->addContent($content);
        $block = 'Webkul\ImageGallery\Block\Adminhtml\Gallery\Edit\Tabs';
        $left = $resultPage->getLayout()->createBlock($block);
        $resultPage->addLeft($left);
        return $resultPage;
    }
}
