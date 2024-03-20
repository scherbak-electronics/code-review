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
namespace Webkul\ImageGallery\Model;

use Webkul\ImageGallery\Api\Data\GalleryInterface;
use Magento\Framework\DataObject\IdentityInterface;

class Gallery extends \Magento\Framework\Model\AbstractModel implements GalleryInterface, IdentityInterface
{
    /**
     * No route page id
     */
    const NOROUTE_ENTITY_ID = 'no-route';

    /**
     * ImageGallery Gallery cache tag
     */
    const CACHE_TAG = 'imagegallery_gallery';

    /**
     * @var string
     */
    protected $_cacheTag = 'imagegallery_gallery';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'imagegallery_gallery';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Webkul\ImageGallery\Model\ResourceModel\Gallery');
    }

    /**
     * Load object data
     *
     * @param int|null $id
     * @param string $field
     * @return $this
     */
    public function load($id, $field = null)
    {
        if ($id === null) {
            return $this->noRouteGallery();
        }
        return parent::load($id, $field);
    }

    /**
     * Load No-Route Gallery
     *
     * @return \Webkul\ImageGallery\Model\Gallery
     */
    public function noRouteGallery()
    {
        return $this->load(self::NOROUTE_ENTITY_ID, $this->getIdFieldName());
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * Get ID
     *
     * @return int
     */
    public function getId()
    {
        return parent::getData(self::ENTITY_ID);
    }

    /**
     * Set ID
     *
     * @param int $id
     * @return \Webkul\ImageGallery\Api\Data\GalleryInterface
     */
    public function setId($id)
    {
        return $this->setData(self::ENTITY_ID, $id);
    }
}
