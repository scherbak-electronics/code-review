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
namespace Webkul\ImageGallery\Block\Gallery;

use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\UrlInterface;

class Gallery extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlBuilder;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $_filesystem;

    /**
     * @var \Webkul\ImageGallery\Model\ResourceModel\Images\CollectionFactory
     */
    protected $_imagesCollection;

    /**
     * @var \Webkul\ImageGallery\Model\ResourceModel\Gallery\CollectionFactory
     */
    protected $_galleryCollection;

    /**
     * @var \Webkul\ImageGallery\Model\ResourceModel\Groups\CollectionFactory
     */
    protected $_groupCollection;

    /**
     * @var \Magento\Framework\Image\AdapterFactory
     */
    protected $_imageFactory;

    /**
     * @var \Magento\Framework\Filesystem\Driver\File
     */
    protected $_fileDriver;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Webkul\ImageGallery\Model\ResourceModel\Images\CollectionFactory $imagesCollection
     * @param \Webkul\ImageGallery\Model\ResourceModel\Gallery\CollectionFactory $galleryCollection
     * @param \Webkul\ImageGallery\Model\ResourceModel\Groups\CollectionFactory $groupCollection
     * @param \Magento\Framework\Image\AdapterFactory $imageFactory
     * @param \Magento\Framework\Filesystem\Driver\File $fileDriver
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Webkul\ImageGallery\Model\ResourceModel\Images\CollectionFactory $imagesCollection,
        \Webkul\ImageGallery\Model\ResourceModel\Gallery\CollectionFactory $galleryCollection,
        \Webkul\ImageGallery\Model\ResourceModel\Groups\CollectionFactory $groupCollection,
        \Magento\Framework\Image\AdapterFactory $imageFactory,
        \Magento\Framework\Filesystem\Driver\File $fileDriver,
        array $data = []
    ) {
        $this->_request = $context->getRequest();
        $this->_scopeConfig = $context->getScopeConfig();
        $this->_storeManager = $context->getStoreManager();
        $this->_urlBuilder = $context->getUrlBuilder();
        $this->_filesystem = $context->getFilesystem();
        $this->_imagesCollection = $imagesCollection;
        $this->_galleryCollection = $galleryCollection;
        $this->_groupCollection = $groupCollection;
        $this->_imageFactory = $imageFactory;
        $this->_fileDriver = $fileDriver;
        parent::__construct($context, $data);
        $this->_values = $this->getValues();
    }

    /**
     * Prepare layout.
     *
     * @return this
     */
    public function _prepareLayout()
    {
        $this->pageConfig->getTitle()->set('Gallery');
        return parent::_prepareLayout();
    }

    /**
     * Get Images Collection.
     *
     * @return collection object
     */
    public function getImagesCollection()
    {
        $collection = $this->_imagesCollection->create();
        $collection->addFieldToFilter('status', 1);
        return $collection;
    }

    /**
     * Get Gallery Collection.
     *
     * @return collection object
     */
    public function getGalleryCollection()
    {
        $collection = $this->_galleryCollection->create();
        $collection->addFieldToFilter('status', 1);
        return $collection;
    }

    /**
     * Get Groups Collection.
     *
     * @return collection object
     */
    public function getGroupCollection()
    {
        $collection = $this->_groupCollection->create();
        $collection->addFieldToFilter('status', 1);
        return $collection;
    }

    /**
     * Get Gallery Ids From Group Code.
     *
     * @param int $groupCode
     *
     * @return array|null
     */
    public function getGalleryIdsFromGroupCode($groupCode)
    {
        $groupCollection = $this->getGroupCollection();
        $groupCollection->addFieldToFilter('group_code', $groupCode);
        $galleryIds = '';
        foreach ($groupCollection as $group) {
            $galleryIds = $group->getGalleryIds();
            break;
        }
        if ($galleryIds != '') {
            if (strpos($galleryIds, ',') !== false) {
                return explode(',', $galleryIds);
            } else {
                return $galleryIds;
            }
        } else {
            return '';
        }
    }

    /**
     * Get Gallery Collection by Selected Gallery Ids.
     *
     * @param int|array $galleryIds
     *
     * @return Collection|null
     */
    public function getSelectedGalleryCollection($galleryIds)
    {
        $galleryCollection = $this->getGalleryCollection();
        if (is_array($galleryIds)) {
            $galleryCollection->addFieldToFilter('id', ['in' => $galleryIds]);
        } else {
            $galleryCollection->addFieldToFilter('id', $galleryIds);
        }
        return $galleryCollection;
    }

    /**
     * Get Images Collection by Selected Image Ids.
     *
     * @param int|array $imagesIds
     *
     * @return Collection|null
     */
    public function getSelectedImagesCollection($imagesIds)
    {
        $imagesCollection = $this->getImagesCollection();
        if (is_array($imagesIds)) {
            $imagesCollection->addFieldToFilter('id', ['in' => $imagesIds]);
        } else {
            $imagesCollection->addFieldToFilter('id', $imagesIds);
        }
        $imagesCollection->setOrder('sort_order', 'ASC');
        return $imagesCollection;
    }

    /**
     * Get Gallery Images by Gallery Id.
     *
     * @param int $galleryId
     *
     * @return Collection|null
     */
    public function getGalleryImagesCollection($galleryId)
    {
        $galleryCollection = $this->getGalleryCollection()
                                ->addFieldToFilter('id', $galleryId);
        if ($galleryCollection->getSize()) {
            foreach ($galleryCollection as $gallery) {
                $imageIds = $gallery->getImageIds();
                if ($imageIds) {
                    if (strpos($imageIds, ',') !== false) {
                        $imageIds = explode(',', $imageIds);
                    }
                    return $this->getSelectedImagesCollection($imageIds);
                }
            }
        }
    }

    /**
     * Get Galley Image Url.
     *
     * @param string $image
     *
     * @return string
     */
    public function getImageUrl($image)
    {
        $mediaUrl = $this->getMediaUrl();
        $imageUrl = $mediaUrl.$image;
        return $imageUrl;
    }

    /**
     * Get Resize Image Url.
     *
     * @param string $image
     *
     * @return string
     */
    public function getResizedUrl($image)
    {
        $mediaUrl = $this->getMediaUrl();
        $imageUrl = $mediaUrl.'resized/'.$image;
        $imagePath = $this->getMediaPath().'resized/'.$image;
        if (!$this->_fileDriver->isExists($imagePath)) {
            $this->resizeImage($image);
        }
        return $imageUrl;
    }

    /**
     * Get Galley Url.
     *
     * @param int $id
     *
     * @return string
     */
    public function getGalleryUrl($id)
    {
        return $this->_urlBuilder->getUrl('imagegallery/gallery/view/').'gallery/'.$id;
    }

    /**
     * Get Sytem Configuration Gallery Settings.
     *
     * @return array
     */
    public function getValues()
    {
        $options = [];
        $sectionId = 'imagegallery';
        $groupId = 'settings';
        $optionArray = [
                        'opening_effect',
                        'closing_effect',
                        'caption',
                        'type',
                        'position',
                        'background',
                        'thumbs',
                        'cyclic',
                        'autoplay',
                        'interval',
                        'mousewheel',
                        'border',
                        'slidecount',
                        'controls'
                    ];
        foreach ($optionArray as $option) {
            $value = $sectionId.'/'.$groupId.'/'.$option;
            $value = $this->_scopeConfig->getValue($value);
            $options[$option] = $value;
        }

        return $options;
    }

    /**
     * Get Galley Settings in Json/Array Format.
     *
     * @param bool $isArray(optional)
     *
     * @return json
     */
    public function getGalleryJsonConfig($isArray = false)
    {
        $options = [];
        $options['openEffect'] = $this->getEffect('opening_effect');
        $options['closeEffect'] = $this->getEffect('closing_effect');
        $options['loop'] = $this->getValue('cyclic');
        $options['slidecount'] = $this->getValue('slidecount');
        $options['playSpeed'] = $this->getInterval();
        $options['helpers']['title'] = $this->getCaption();
        $options['helpers']['overlay'] = $this->getOverlay();
        $options['padding'] = $this->getBorder();
        $options['autoPlay'] = $this->getAutoplay();
        $options['helpers']['thumbs'] = $this->getThumbnail();
        $options['helpers']['buttons'] = $this->getControls();
        if ($isArray) {
            return $options;
        }
        return json_encode($options);
    }

    /**
     * Get Galley Settings for Autoplay.
     *
     * @return bool
     */
    public function getAutoplay()
    {
        $values = $this->_values;
        if (!$values['controls']) {
            if ($values['autoplay']) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get Galley Settings for Thumbnail.
     *
     * @return bool | array
     */
    public function getThumbnail()
    {
        $values = $this->_values;
        if (!$values['controls']) {
            if ($values['thumbs']) {
                $thumbSizes = [40, 80, 120, 160, 200];
                $config = 'imagegallery/settings/thumb_type';
                $thumbType = (int) $this->_scopeConfig->getValue($config);
                $thumbSize = $thumbSizes[$thumbType];
                $thumbs = ['width' => $thumbSize, 'height' => $thumbSize];
                return $thumbs;
            }
        }
        return false;
    }

    /**
     * Get Galley Settings for Control Box.
     *
     * @return bool | array
     */
    public function getControls()
    {
        $values = $this->_values;
        if ($values['controls']) {
            return "{}";
        }
        return false;
    }

    /**
     * Get Galley Settings for Border.
     *
     * @return bool | int
     */
    public function getBorder()
    {
        $values = $this->_values;
        if ($values['border']) {
            return 10;
        }
        return 0;
    }

    /**
     * Get Galley Settings for interval.
     *
     * @return int
     */
    public function getInterval()
    {
        $values = $this->_values;
        if ($values['interval'] != "") {
            if ((int) $values['interval'] > 0) {
                return $values['interval'];
            }
        }
        return 1000;
    }

    /**
     * Get Galley Settings for Caption.
     *
     * @return bool | array
     */
    public function getCaption()
    {
        $values = $this->_values;
        $title = [
                    'type' => 'inside',
                    'position' => 'bottom',
                ];
        if ($values['caption']) {
            if ($values['type'] != '') {
                $title['type'] = $values['type'];
            }
            if ($values['position'] != '') {
                $title['position'] = $values['position'];
            }
            return $title;
        }
        return false;
    }

    /**
     * Get Galley Settings for Background.
     *
     * @return array
     */
    public function getOverlay()
    {
        $values = $this->_values;
        $background = [];
        if ($values['background'] == 'dark') {
            $background['css']['background'] = 'rgba(0, 0, 0, 0.7)';
        } else {
            $background['css']['background'] = 'rgba(255, 255, 255, 0.7)';
        }
        return $background;
    }

    /**
     * Get Galley Settings.
     *
     * @param string $index
     *
     * @return string
     */
    public function getValue($index)
    {
        $values = $this->_values;
        $value = $values[$index];
        if ($value) {
            return $value;
        }
        return false;
    }

    /**
     * Get Galley Settings for Effect.
     *
     * @param string $index
     *
     * @return string
     */
    public function getEffect($index)
    {
        $values = $this->_values;
        $value = $values[$index];
        if ($value == "") {
            return 'none';
        }
        return $value;
    }

    /**
     * Get Media Path.
     *
     * @return string
     */
    public function getMediaPath()
    {
        $path = $this->_filesystem
                    ->getDirectoryRead(DirectoryList::MEDIA)
                    ->getAbsolutePath();
        return $path;
    }

    /**
     * Resize Image.
     *
     * @param string $image
     *
     * @return string
     */
    public function resizeImage($image)
    {
        $mediaUrl = $this->getMediaUrl();
        $mediaPath = $this->getMediaPath();
        $imagePath = $mediaPath.$image;
        $destination = $mediaPath.'resized/imagegallery/images/';
        if (!$this->_fileDriver->isExists($destination)) {
            $this->_fileDriver->createDirectory($destination, 0755);
        }
        if (!$this->_fileDriver->isExists($imagePath)) {
            return false;
        }
        $resizedImage = $mediaPath.'resized/'.$image;
        $imageUploader = $this->_imageFactory->create();
        $imageUploader->open($imagePath);
        $imageUploader->backgroundColor([255, 255, 255]);
        $imageUploader->constrainOnly(true);
        $imageUploader->keepTransparency(true);
        $imageUploader->keepFrame(true);
        $imageUploader->keepAspectRatio(true);
        $imageUploader->resize(200, 200);
        $imageUploader->save($resizedImage);
        $resizedURL = $mediaUrl.'resized/'.$image;
        return $resizedURL;
    }

    /**
     * Get Galley Id.
     *
     * @return int
     */
    public function getGalleryId()
    {
        $galleryCode = $this->getGalleryCode();
        if ($galleryCode != "") {
            $galleryId = $this->getGalleryIdByCode();
        } else {
            $galleryId = (int) $this->_request->getParam('gallery');
        }
        return $galleryId;
    }

    /**
     * Get Galley Id Gallery Code.
     *
     * @return int
     */
    public function getGalleryIdByCode()
    {
        $galleryId = 0;
        $galleryCode = $this->getGalleryCode();
        $collection = $this->getGalleryCollection()
                            ->addFieldToFilter('gallery_code', $galleryCode);
        if ($collection->getSize()) {
            foreach ($collection as $gallery) {
                $galleryId = $gallery->getId();
            }
        }
        return $galleryId;
    }

    /**
     * Get Galley Images.
     *
     * @return object
     */
    public function getGalleryImages()
    {
        $galleryId = $this->getGalleryId();
        $imagesCollection = $this->getGalleryImagesCollection($galleryId);
        return $imagesCollection;
    }

    /**
     * Get Media Url.
     *
     * @return string
     */
    public function getMediaUrl()
    {
        $type = UrlInterface::URL_TYPE_MEDIA;
        return $this->_storeManager->getStore()->getBaseUrl($type);
    }
}
