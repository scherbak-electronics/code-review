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
namespace Webkul\ImageGallery\Block\Widget\Grid\Column\Renderer;

use Magento\Store\Model\StoreManagerInterface;

class Image extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
    * @var Magento\Store\Model\StoreManagerInterface
    */
    protected $_storeManager;

    /**
    * @param \Magento\Backend\Block\Context $context
    * @param StoreManagerInterface $storemanager
    * @param array $data
    */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        StoreManagerInterface $storemanager,
        array $data = []
    ) {
        $this->_storeManager = $storemanager;
        parent::__construct($context, $data);
    }

    /**
    * Renders grid column
    *
    * @param \Magento\Framework\DataObject $row
    *
    * @return string
    */
    public function render(\Magento\Framework\DataObject $row)
    {
        $mediaDirectory = $this->_storeManager->getStore()->getBaseUrl(
            \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
        );
        $imageUrl = $mediaDirectory.$this->_getValue($row);
        return '<img src="'.$imageUrl.'" width="100"/>';
    }
}
