<?php
/**
 * Copyright © Pronko Consulting
 * See LICENSE for license details.
 */

namespace Pronko\Elavon\Block\Adminhtml\System\Config;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\Data\Form\Element\Factory;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\DataObject;
use Pronko\Elavon\Block\Adminhtml\System\Config\Field\CardTypes;

/**
 * Sub-Account config field renderer
 */
class SubAccount extends AbstractFieldArray
{
    /**
     * @var \Magento\Framework\Data\Form\Element\Factory
     */
    private $elementFactory;

    /**
     * @var CardTypes
     */
    private $cardTypesRenderer;

    /**
     * @param Context $context
     * @param Factory $elementFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        Factory $elementFactory,
        array $data = []
    ) {
        $this->elementFactory = $elementFactory;
        parent::__construct($context, $data);
    }

    /**
     * Initialise form fields
     *
     * @return void
     */
    protected function _construct() // @codingStandardsIgnoreLine MEQP2.PHP.ProtectedClassMember.FoundProtected
    {
        $this->addColumn(
            'name',
            [
                'label' => __('Name')
            ]
        );
        $this->addColumn(
            'card_types',
            [
                'label' => __('Card Type'),
                'renderer' => $this->getCardTypesRenderer()
            ]
        );
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add Sub Account');
        parent::_construct();
    }

    /**
     * @return CardTypes
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getCardTypesRenderer()
    {
        if (!$this->cardTypesRenderer) {
            $this->cardTypesRenderer = $this->getLayout()->createBlock(
                CardTypes::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
        }
        return $this->cardTypesRenderer;
    }

    /**
     * Prepare existing row data object
     *
     * @param DataObject $row
     * @return void
     */
    protected function _prepareArrayRow(DataObject $row) // @codingStandardsIgnoreLine MEQP2.PHP.ProtectedClassMember.FoundProtected
    {
        $name = $row->getName();
        $options = [];
        if ($name) {
            $cardTypes = $row->getCardTypes();
            foreach ($cardTypes as $cardType) {
                $options['option_' . $this->getCardTypesRenderer()->calcOptionHash($cardType)]
                    = 'selected="selected"';
            }
        }
        $row->setData('option_extra_attrs', $options);
    }
}
