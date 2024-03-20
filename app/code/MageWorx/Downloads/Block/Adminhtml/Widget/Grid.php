<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Downloads\Block\Adminhtml\Widget;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\Element\Factory as ElementFactory;

abstract class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var ElementFactory
     */
    protected $elementFactory;

    /**
     * @var array
     */
    protected $selectedItems = [];

    /**
     * @var string
     */
    protected $elementValueId = '';

    /**
     * Grid constructor.
     *
     * @param Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param ElementFactory $elementFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        ElementFactory $elementFactory,
        array $data = []
    ) {
        parent::__construct($context, $backendHelper, $data);
        $this->elementFactory = $elementFactory;
    }

    /**
     * @param AbstractElement $element
     * @return AbstractElement
     */
    public function prepareElementHtml(AbstractElement $element)
    {
        $this->elementValueId = "{$element->getId()}";
        $this->selectedItems  = explode(',', $element->getValue());

        $hiddenField = $this->elementFactory->create('hidden', ['data' => $element->getData()]);
        $hiddenField
            ->setId($this->elementValueId)
            ->setForm($element->getForm());

        $element
            ->setData('after_element_html', $hiddenField->getElementHtml() . $this->toHtml())
            ->setData('css_class', 'grid-chooser')
            ->setData('no_wrap_as_addon', true)
            ->setValue('')
            ->setValueClass('value2');

        return $element;
    }

    /**
     * Grid row init js callback
     *
     * @return string
     */
    public function getRowInitCallback()
    {
        return '
            function(grid, row){
            
                if (!grid.selItemsIds) {
                    grid.selItemsIds = {};
                    
                    if ($(\'' . $this->elementValueId . '\').value != \'\') {
                        var elementValues = $(\'' . $this->elementValueId . '\').value.split(\',\');
                        
                        for (var i = 0; i < elementValues.length; i++) {
                            grid.selItemsIds[elementValues[i]] = 1;
                        }
                    }
                    
                    grid.reloadParams = {};
                    grid.reloadParams[\'selected_items[]\'] = Object.keys(grid.selItemsIds);
                }
                
                var inputs   = Element.select($(row), \'input\');
                var checkbox = inputs[0];
                var itemId   = checkbox.value;
                var indexOf  = Object.keys(grid.selItemsIds).indexOf(itemId);
                
                if (indexOf >= 0) {
                    checkbox.checked = true;
                }
            }
        ';
    }

    /**
     * Grid Row JS Callback
     *
     * @return string
     */
    public function getRowClickCallback(): string
    {
        return '
            function (grid, event) {
            
                if (!grid.selItemsIds) {
                    grid.selItemsIds = {};
                }

                var trElement    = Event.findElement(event, "tr");
                var isInput      = Event.element(event).tagName == \'INPUT\';
                var inputs       = Element.select(trElement, \'input\');
                var checkbox     = inputs[0];
                var checked      = isInput ? checkbox.checked : !checkbox.checked;
                checkbox.checked = checked;
                var itemId       = checkbox.value;

                if (checked) {
                    if (Object.keys(grid.selItemsIds).indexOf(itemId) < 0) {
                        grid.selItemsIds[itemId] = 1;
                    }
                } else {
                    delete(grid.selItemsIds[itemId]);
                }
                
                var items = Object.keys(grid.selItemsIds);

                $(\'' . $this->elementValueId . '\').value = items.join(\',\');
                grid.reloadParams = {};
                grid.reloadParams[\'selected_items[]\'] = items;
            }
        ';
    }

    /**
     * Checkbox Check JS Callback
     *
     * @return string
     */
    public function getCheckboxCheckCallback()
    {
        return '
            function (grid, element, checked) {
        
                if (!grid.selItemsIds) {
                    grid.selItemsIds = {};
                }
                
                var checkbox     = element;
                checkbox.checked = checked;
                var itemId       = checkbox.value;
                
                if (itemId == \'on\') {
                    return;
                }
             
                if (checked) {
                    if (Object.keys(grid.selItemsIds).indexOf(itemId) < 0) {
                        grid.selItemsIds[itemId] = 1;
                    }
                } else{
                    delete(grid.selItemsIds[itemId]);
                }
                
                var items = Object.keys(grid.selItemsIds);
                
                $(\'' . $this->elementValueId . '\').value = items.join(\',\');
                grid.reloadParams = {};
                grid.reloadParams[\'selected_items[]\'] = items;
            }
        ';
    }

    /**
     * @return array
     */
    public function getSelectedItems()
    {
        $selectedItems = $this->getRequest()->getParam('selected_items', $this->selectedItems);

        if ($selectedItems) {
            $this->setSelectedItems($selectedItems);
        }

        return $this->selectedItems;
    }

    /**
     * @param array|string $selectedItems
     * @return $this
     */
    public function setSelectedItems($selectedItems)
    {
        if (is_string($selectedItems)) {
            $selectedItems = explode(',', $selectedItems);
        }

        $this->selectedItems = $selectedItems;

        return $this;
    }
}
