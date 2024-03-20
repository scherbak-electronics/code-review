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

class Images extends \Webkul\ImageGallery\Controller\Adminhtml\Gallery
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
     * @var \Magento\Framework\View\Result\LayoutFactory
     */
    protected $_resultLayoutFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Webkul\ImageGallery\Model\GalleryFactory $gallery
     * @param \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $registry,
        \Webkul\ImageGallery\Model\GalleryFactory $gallery,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
    ) {
        $this->_backendSession = $context->getSession();
        $this->_registry = $registry;
        $this->_gallery = $gallery;
        $this->_resultLayoutFactory = $resultLayoutFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\View\Result\Layout
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
        $resultLayout = $this->_resultLayoutFactory->create();
        $resultLayout->getLayout()
                    ->getBlock('imagegallery.gallery.edit.tab.images');
        return $resultLayout;
    }
}
