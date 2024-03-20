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

use Webkul\ImageGallery\Api\Data\ImagesInterface;
use Magento\Framework\DataObject\IdentityInterface;

class Images extends \Magento\Framework\Model\AbstractModel implements ImagesInterface, IdentityInterface
{
    /**
     * No route page id
     */
    const NOROUTE_ENTITY_ID = 'no-route';

    /**#@+
     * Images's Status
     */
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;
    /**#@-*/

    /**
     * ImageGallery Images cache tag
     */
    const CACHE_TAG = 'imagegallery_images';

    /**
     * @var string
     */
    protected $_cacheTag = 'imagegallery_images';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'imagegallery_images';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Webkul\ImageGallery\Model\ResourceModel\Images');
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
            return $this->noRouteImages();
        }
        return parent::load($id, $field);
    }

    /**
     * Load No-Route Images
     *
     * @return \Webkul\ImageGallery\Model\Images
     */
    public function noRouteImages()
    {
        return $this->load(self::NOROUTE_ENTITY_ID, $this->getIdFieldName());
    }

    /**
     * Prepare seller's statuses.
     * Available event imagegallery_images_get_available_statuses to customize statuses.
     *
     * @return array
     */
    public function getAvailableStatuses()
    {
        return [self::STATUS_ENABLED => __('Approved'), self::STATUS_DISABLED => __('Disapproved')];
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
     * @return \Webkul\ImageGallery\Api\Data\ImagesInterface
     */
    public function setId($id)
    {
        return $this->setData(self::ENTITY_ID, $id);
    }
}
