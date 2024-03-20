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

use Magento\Backend\App\Action;

class Save extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $_backendSession;

    /**
     * @var \Webkul\ImageGallery\Model\GroupsFactory
     */
    protected $_groups;

    /**
     * @param Action\Context $context
     * @param \Webkul\ImageGallery\Model\GroupsFactory $groups
     */
    public function __construct(
        Action\Context $context,
        \Webkul\ImageGallery\Model\GroupsFactory $groups
    ) {
        $this->_backendSession = $context->getSession();
        $this->_groups = $groups;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webkul_ImageGallery::groups');
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
        $galleryIds = $this->getRequest()->getParam('gallery_ids', null);
        parse_str($galleryIds, $galleryIds);
        $galleryIds = array_keys($galleryIds);
        $galleryIds = implode(",", $galleryIds);
        $groupsModel = $this->_groups->create();
        $collection = $groupsModel->getCollection();
        $collection->addFieldToFilter('group_code', $data['group_code']);
        foreach ($collection as $item) {
            if ($item->getId()) {
                $flag = true;
                $reserveId = $item->getId();
                break;
            }
        }
        if (!empty($data)) {
            $groupData = [];
            $groupData['group_code'] = $data['group_code'];
            $groupData['gallery_ids'] = $galleryIds;
            $groupData['status'] = $data['status'];
            $model = $this->_groups->create();
            $id = (int) $this->getRequest()->getParam('id');
            $groupData['updated_time'] = $time;
            $error = 'Group code already exist';
            if ($id > 0) {
                if ($id != $reserveId) {
                    if ($flag) {
                        $this->messageManager->addError(__($error));
                        $params = ['id' => $id, '_current' => true];
                        return $resultRedirect->setPath('*/*/edit', $params);
                    }
                }
                $model->addData($groupData)->setId($id)->save();
            } else {
                if ($flag) {
                    $this->_backendSession->setFormData($data);
                    $this->messageManager->addError(__($error));
                    return $resultRedirect->setPath('*/*/new');
                }
                $groupData['created_time'] = $time;
                $model->setData($groupData)->save();
            }
            $this->messageManager->addSuccess(__('Group saved succesfully'));
        } else {
            $this->messageManager->addError(__('Something went wrong'));
        }
        return $resultRedirect->setPath('*/*/');
    }
}
