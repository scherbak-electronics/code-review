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

use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Webkul\ImageGallery\Model\ResourceModel\Gallery\CollectionFactory;
use Webkul\ImageGallery\Model\ResourceModel\Groups\CollectionFactory as GroupsCollection;

class MassDelete extends \Magento\Backend\App\Action
{
    /**
     * @var Filter
     */
    protected $_filter;

    /**
     * @var CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var GroupsCollection
     */
    protected $_groupsCollectionFactory;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param GroupsCollection $groupsCollectionFactory
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        GroupsCollection $groupsCollectionFactory
    ) {
        $this->_filter = $filter;
        $this->_collectionFactory = $collectionFactory;
        $this->_groupsCollectionFactory = $groupsCollectionFactory;
        parent::__construct($context);
    }

    /**
    * {@inheritdoc}
    */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webkul_ImageGallery::gallery');
    }

    /**
    * @return \Magento\Framework\Controller\ResultInterface
    */
    public function execute()
    {
        $galleryIds = [];
        $collection = $this->_filter->getCollection($this->_collectionFactory->create());
        foreach ($collection as $gallery) {
            $galleryIds[] = $gallery->getId();
            $this->removeItem($gallery);
        }
        $this->updateAllGroups($galleryIds);
        $this->messageManager->addSuccess(__('Gallery(s) deleted succesfully'));
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Remove Gallery
     *
     * @param object $item
    */
    public function removeItem($item)
    {
        $item->delete();
    }

    /**
     * Update All Groups
     *
     * @param array $deletedGalleryIds
    */
    public function updateAllGroups($deletedGalleryIds)
    {
        $collection = $this->_groupsCollectionFactory
                            ->create()
                            ->addFieldToSelect('id')
                            ->addFieldToSelect('gallery_ids');
        if ($collection->getSize()) {
            foreach ($collection as $groups) {
                $galleryIds = trim($groups->getGalleryIds());
                if ($galleryIds) {
                    if (strpos($galleryIds, ',') !== false) {
                        $galleryIds = explode(',', $galleryIds);
                    } else {
                        $galleryIds = [];
                    }
                } else {
                    $galleryIds = [];
                }
                $result = array_intersect($deletedGalleryIds, $galleryIds);
                if (!empty($result) > 0) {
                    $result = array_diff($galleryIds, $deletedGalleryIds);
                    $gallery = implode(",", $result);
                    $this->updateGroup($groups, $gallery);
                }
            }
        }
    }

    /**
     * Update Group
     *
     * @param object $groups
     * @param array $gallery
    */
    public function updateGroup($groups, $gallery)
    {
        $groups->addData(["gallery_ids" => $gallery])
                ->setId($groups->getId())
                ->save();
    }
}
