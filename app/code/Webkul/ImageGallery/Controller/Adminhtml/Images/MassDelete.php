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

use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Webkul\ImageGallery\Model\ResourceModel\Images\CollectionFactory;
use Webkul\ImageGallery\Model\ResourceModel\Gallery\CollectionFactory as GalleryCollection;

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
     * @var GalleryCollection
     */
    protected $_galleryCollectionFactory;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param GalleryCollection $galleryCollectionFactory
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        GalleryCollection $galleryCollectionFactory
    ) {
        $this->_filter = $filter;
        $this->_collectionFactory = $collectionFactory;
        $this->_galleryCollectionFactory = $galleryCollectionFactory;
        parent::__construct($context);
    }

    /**
    * {@inheritdoc}
    */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webkul_ImageGallery::images');
    }

    /**
    * @return \Magento\Framework\Controller\ResultInterface
    */
    public function execute()
    {
        $imageIds = [];
        $collection = $this->_filter->getCollection($this->_collectionFactory->create());
        foreach ($collection as $image) {
            $imageIds[] = $image->getId();
            $this->removeItem($image);
        }
        $this->updateAllGallery($imageIds);
        $this->messageManager->addSuccess(__('Image(s) deleted succesfully'));
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Remove Image
     *
     * @param object $item
    */
    public function removeItem($item)
    {
        $item->delete();
    }

    /**
     * Update All Gallery
     *
     * @param array $deletedImageIds
    */
    public function updateAllGallery($deletedImageIds)
    {
        $collection = $this->_galleryCollectionFactory
                            ->create()
                            ->addFieldToSelect('id')
                            ->addFieldToSelect('image_ids');
        if ($collection->getSize()) {
            foreach ($collection as $gallery) {
                $imageIds = trim($gallery->getImageIds());
                if ($imageIds) {
                    if (strpos($imageIds, ',') !== false) {
                        $imageIds = explode(',', $imageIds);
                    } else {
                        $imageIds = [];
                    }
                } else {
                    $imageIds = [];
                }
                $result = array_intersect($deletedImageIds, $imageIds);
                if (!empty($result) > 0) {
                    $result = array_diff($imageIds, $deletedImageIds);
                    $images = implode(",", $result);
                    $this->updateGallery($gallery, $images);
                }
            }
        }
    }

    /**
     * Update Gallery
     *
     * @param object $gallery
     * @param array $images
    */
    public function updateGallery($gallery, $images)
    {
        $gallery->addData(["image_ids" => $images])
                ->setId($gallery->getId())
                ->save();
    }
}
