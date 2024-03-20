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

use Magento\Backend\App\Action;

class Save extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $_backendSession;

    /**
     * @var \Webkul\ImageGallery\Model\GalleryFactory
     */
    protected $_gallery;

    /**
     * @param Action\Context $context
     * @param \Webkul\ImageGallery\Model\GalleryFactory $gallery
     */
    public function __construct(
        Action\Context $context,
        \Webkul\ImageGallery\Model\GalleryFactory $gallery
    ) {
        $this->_backendSession = $context->getSession();
        $this->_gallery = $gallery;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization
                    ->isAllowed('Webkul_ImageGallery::gallery');
    }

    /**
     * Save action.
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $flag = false;
        $reserveId = 0;
        $time = date('Y-m-d H:i:s');
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        $imageIds = $this->getRequest()->getParam('image_ids', null);
        parse_str($imageIds, $imageIds);
        $imageIds = array_keys($imageIds);
        $imageIds = implode(",", $imageIds);
        $galleryModel = $this->_gallery->create();
        $collection = $galleryModel->getCollection();
        $collection->addFieldToFilter('gallery_code', $data['gallery_code']);
        foreach ($collection as $item) {
            if ($item->getId()) {
                $flag = true;
                $reserveId = $item->getId();
                break;
            }
        }
        if (!empty($data)) {
            $error = 'Gallery code already exist';
            $model = $this->_gallery->create();
            $id = $this->getRequest()->getParam('id');
            $data['updated_time'] = $time;
            if (array_key_exists('thumb', $data)) {
                $data['thumbnail_show'] = $data['thumb'];
            }
            $data['image_ids'] = $imageIds;
            if ($id) {
                if ($id != $reserveId) {
                    if ($flag) {
                        $this->messageManager
                            ->addError(__($error));
                        $params = ['id' => $id, '_current' => true];
                        return $resultRedirect->setPath('*/*/edit', $params);
                    }
                }
                $model->addData($data)->setId($id)->save();
            } else {
                if ($flag) {
                    $this->_backendSession->setFormData($data);
                    $this->messageManager->addError(__($error));
                    return $resultRedirect->setPath('*/*/new');
                }
                $data['created_time'] = $time;
                $model->setData($data)->save();
            }
            $this->messageManager->addSuccess(__('Gallery saved succesfully'));
        } else {
            $this->messageManager->addError(__('Something went wrong'));
        }
        return $resultRedirect->setPath('*/*/');
    }
}
