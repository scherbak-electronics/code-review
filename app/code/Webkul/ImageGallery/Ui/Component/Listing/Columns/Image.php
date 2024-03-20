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
namespace Webkul\ImageGallery\Ui\Component\Listing\Columns;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\UrlInterface;

class Image extends \Magento\Ui\Component\Listing\Columns\Column
{
    const NAME = 'image';

    const ALT_FIELD = 'name';

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Webkul\ImageGallery\Model\ImagesFactory $images
     */
    protected $_images;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param StoreManagerInterface $storemanager
     * @param \Webkul\ImageGallery\Model\ImagesFactory $images
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        StoreManagerInterface $storemanager,
        \Webkul\ImageGallery\Model\ImagesFactory $images,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->_storeManager = $storemanager;
        $this->_images = $images;
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        $mediaDirectory = $this->_storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as & $item) {
                $images = $this->getImage($item['id']);
                $imageName = $images->getImage();
                $imageTitle = $images->getTitle();
                $item[$fieldName . '_src'] = $mediaDirectory.$imageName;
                $item[$fieldName . '_alt'] = $this->getAlt($item) ?: $imageTitle;
                $item[$fieldName . '_orig_src'] = $mediaDirectory.$imageName;
            }
        }
        return $dataSource;
    }

    /**
     * Get Image Object
     *
     * @param int $id
     *
     * @return object
     */
    public function getImage($id)
    {
        return $this->_images->create()->load($id);
    }
}
