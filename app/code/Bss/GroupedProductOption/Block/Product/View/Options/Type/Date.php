<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_GroupedProductOption
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\GroupedProductOption\Block\Product\View\Options\Type;

use Magento\Catalog\Helper\Data as CatalogHelper;
use Magento\Catalog\Model\Product\Option\Type\Date as OptionDateModel;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Pricing\Helper\Data as PricingHelper;
use Magento\Framework\View\Element\Template\Context;

class Date extends \Magento\Catalog\Block\Product\View\Options\Type\Date
{
    /**
     * @var DataObjectFactory
     */
    protected $dataObjectFactory;

    /**
     * Date constructor.
     * @param Context $context
     * @param PricingHelper $pricingHelper
     * @param CatalogHelper $catalogData
     * @param OptionDateModel $catalogProductOptionTypeDate
     * @param DataObjectFactory $dataObjectFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        PricingHelper $pricingHelper,
        CatalogHelper $catalogData,
        OptionDateModel $catalogProductOptionTypeDate,
        DataObjectFactory $dataObjectFactory,
        array $data = []
    ) {
        $this->dataObjectFactory = $dataObjectFactory;
        parent::__construct($context, $pricingHelper, $catalogData, $catalogProductOptionTypeDate, $data);
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCalendarDateHtml()
    {
        $product = $this->getProduct();
        $option = $this->getOption();
        $productId = $product->getId();
        $value = $product->getPreconfiguredValues()->getData('options/' . $option->getId() . '/date');

        $yearStart = $this->_catalogProductOptionTypeDate->getYearStart();
        $yearEnd = $this->_catalogProductOptionTypeDate->getYearEnd();

        $calendarId = 'options_' . $productId . '_' . $this->getOption()->getId() . '_date';
        $calendarName = 'options_' . $productId . '[' . $this->getOption()->getId() . '][date]';
        $calendarClass = 'product-custom-option datetime-picker input-text';
        $calendarImage = $this->getViewFileUrl('Magento_Theme::calendar.png');
        $dateFormat = $this->_localeDate->getDateFormat(\IntlDateFormatter::SHORT);

        $calendar = $this->getLayout()->createBlock(\Magento\Framework\View\Element\Html\Date::class);
        $calendar->setId($calendarId)
            ->setName($calendarName)
            ->setClass($calendarClass)
            ->setImage($calendarImage)
            ->setDateFormat($dateFormat)
            ->setValue($value)
            ->setYearsRange($yearStart . ':' . $yearEnd);

        return $calendar->getHtml();
    }

    /**
     * @param string $name
     * @param null $value
     * @return \Magento\Framework\View\Element\BlockInterface|mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _getHtmlSelect($name, $value = null)
    {
        $product = $this->getProduct();
        $option = $this->getOption();
        $productId = $product->getId();
        $optionId = $option->getId();
        $this->setSkipJsReloadPrice(1);

        $selectId = 'options_' . $productId . '_' . $optionId . '_' . $name;
        $selectClass = 'product-custom-option admin__control-select datetime-picker';
        $selectName = 'options_' . $productId . '[' . $optionId . '][' . $name . ']';

        $select = $this->getLayout()->createBlock(\Magento\Framework\View\Element\Html\Select::class);
        $select->setId($selectId)
            ->setClass($selectClass)
            ->setExtraParams()
            ->setName($selectName);

        $extraParams = 'style="width:auto"';
        if (!$this->getSkipJsReloadPrice()) {
            $extraParams .= ' onchange="opConfig.reloadPrice()"';
        }
        $extraParams .= ' data-role="calendar-dropdown" data-calendar-role="' . $name . '"';
        $extraParams .= ' data-selector="' . $select->getName() . '"';
        if ($this->getOption()->getIsRequire()) {
            $extraParams .= ' data-validate=\'{"datetime-validation": true}\'';
        }

        $select->setExtraParams($extraParams);
        if ($value === null) {
            $value = $product->getPreconfiguredValues()->getData(
                'options/' . $optionId . '/' . $name
            );
        }
        if ($value !== null) {
            $select->setValue($value);
        }

        return $select;
    }

    /**
     * GetBssCustomOptionBlock
     *
     * @param string $place
     * @return string
     * @throws LocalizedException
     */
    public function getBssCustomOptionBlock($place)
    {
        $childObject = $this->dataObjectFactory->create();

        $this->_eventManager->dispatch(
            'bss_custom_options_render_file_' . $place,
            ['child' => $childObject]
        );
        $blocks = $childObject->getData() ?: [];
        $output = '';

        foreach ($blocks as $childBlock) {
            $block = $this->getLayout()->createBlock($childBlock);
            $block->setProduct($this->getProduct())->setOption($this->getOption());
            $output .= $block->toHtml();
        }
        return $output;
    }
}
