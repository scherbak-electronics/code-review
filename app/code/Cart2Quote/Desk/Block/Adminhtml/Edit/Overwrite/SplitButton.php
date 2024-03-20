<?php
/**
 * Copyright (c) 2019. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Desk\Block\Adminhtml\Edit\Overwrite;

/**
 * Class SplitButton
 * @package Cart2Quote\Desk\Block\Adminhtml\Edit\Overwrite
 */
class SplitButton extends \Magento\Backend\Block\Widget\Button\SplitButton
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry = null;

    /**
     * Class SplitButton constructor
     *
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        $this->coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve button attributes html
     *
     * @return string
     */
    public function getButtonAttributesHtml()
    {
        $attributes = $this->getAttributes(true);
        if ($this->getDataAttribute()) {
            $this->_getDataAttributes($this->getDataAttribute(), $attributes);
        }

        $html = $this->_getAttributesString($attributes);
        $html .= $this->getUiId();

        return $html;
    }

    /**
     * Retrieve toggle button attributes html
     *
     * @return string
     */
    public function getToggleAttributesHtml()
    {
        $attributes = $this->getAttributes(false);
        $this->_getDataAttributes(['mage-init' => '{"dropdown": {}}', 'toggle' => 'dropdown'], $attributes);

        $html = $this->_getAttributesString($attributes);
        $html .= $this->getUiId('dropdown');

        return $html;
    }

    /**
     * @param boolean $attribute
     * @return array
     */
    public function getAttributes($attribute)
    {
        $disabled = $this->getDisabled() ? 'disabled' : '';
        $title = $this->getTitle();
        if (!$title) {
            $title = $this->getLabel();
        }
        $classes = [];
        $classes[] = $attribute ? 'action-default' : 'action-toggle';
        $classes[] = 'primary';

        if ($this->getClass()) {
            $classes[] = $this->getClass();
        }

        if ($disabled) {
            $classes[] = $disabled;
        }

        return $attributes = [
            'id' => $attribute ? $this->getId() . '-button' : 'ticketDropdown',
            'title' => $title,
            'class' => join(' ', $classes),
            'disabled' => $disabled,
            'style' => $attribute ? $this->getStyle() : '',
            'onclick' => $attribute ? $this->getOnClick() : ''// added to original
        ];
    }

    /**
     * Get the onclick event for the default ticket submit button
     *
     * @return string
     */
    public function getOnClick()
    {
        $ticket = $this->coreRegistry->registry('ticket_data');
        $ticketId = 0;
        $statusId = 1;
        $elementId = 'edit_form';
        $quoteId = $this->getQuoteId();

        if (isset($ticket) && $ticket->getId()) {
            $ticketId = $ticket->getId();
            $statusId = $ticket->getStatusId();
        }

        if ($quoteId) {
            $elementId = 'ticket_form';
        }

        $onClick =
            "document.getElementById('$elementId').action = " .
            "'{$this->getTicketSubmitUrl($ticketId, $statusId, $quoteId)}';" .
            " document.getElementById('$elementId').submit();";

        return $onClick;
    }

    /**
     * Get the Ticket submit url by specified status type
     *
     * @param $ticketId
     * @param $statusId
     * @param $quoteId
     * @return string
     */
    protected function getTicketSubmitUrl($ticketId, $statusId, $quoteId)
    {
        if ($quoteId) {
            return $this->getUrl(
                'desk/ticket/quoteticketsave',
                ['id' => $ticketId, 'status_id' => $statusId]
            );
        }

        return $this->getUrl(
            '*/*/save',
            ['id' => $ticketId, 'status_id' => $statusId]
        );
    }

    /**
     * Retrieve quote model instance
     *
     * @return \Cart2Quote\Quotation\Model\Quote|null
     */
    public function getQuote()
    {
        return $this->coreRegistry->registry('current_quote');
    }

    /**
     * Get Quote Id
     *
     * @return int|boolean
     */
    public function getQuoteId()
    {
        if ($this->getQuote()) {
            return $this->getQuote()->getId();
        } else {
            return false;
        }
    }
}
