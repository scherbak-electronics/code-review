<?php
/**
 * Copyright (c) 2019. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Desk\Block\Adminhtml\Quote\View\Tab;

/**
 * Class Save
 * @package Cart2Quote\Desk\Block\Adminhtml\Quote\View\Tab
 */
class Save extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry = null;

    /**
     * Ticket Repository
     *
     * @var \Cart2Quote\Desk\Api\TicketRepositoryInterface
     */
    protected $ticketRepositoryInterface;

    /**
     * Ticket Model
     *
     * @var \Cart2Quote\Desk\Model\Ticket
     */
    protected $ticket;

    /**
     * Status data
     *
     * @var \Cart2Quote\Desk\Model\ResourceModel\Ticket\Status\Collection
     */
    protected $statusCollection = null;

    /**
     * Quote ID
     *
     * @var int
     */
    protected $quoteId;

    /**
     * @var string
     */
    protected $_objectId = 'id';

    /**
     * @var string
     */
    protected $_blockGroup = 'Cart2Quote_Desk';

    /**
     * @var string
     */
    protected $_controller = 'adminhtml';

    /**
     * Save constructor.
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Cart2Quote\Desk\Api\TicketRepositoryInterface $ticketRepositoryInterface
     * @param \Cart2Quote\Desk\Model\Ticket $ticket
     * @param \Magento\Framework\Registry $registry
     * @param \Cart2Quote\Desk\Model\ResourceModel\Ticket\Status\Collection $statusCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Cart2Quote\Desk\Api\TicketRepositoryInterface $ticketRepositoryInterface,
        \Cart2Quote\Desk\Model\Ticket $ticket,
        \Magento\Framework\Registry $registry,
        \Cart2Quote\Desk\Model\ResourceModel\Ticket\Status\Collection $statusCollection,
        array $data = []
    ) {
        $this->coreRegistry = $registry;
        $this->ticketRepositoryInterface = $ticketRepositoryInterface;
        $this->ticket = $ticket;
        $this->statusCollection = $statusCollection;
        parent::__construct($context, $data);
    }

    /**
     * Overwrite: Force the form from layout xml file
     *
     * @return void
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
    }

    /**
     * Initialize edit ticket
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();

        $this->removeButton('save');

        $ticketId = $this->setQuoteTicketId();
        $this->addSubmitButton($ticketId);
    }

    /**
     * Retrieve quote model instance
     *
     * @return \Cart2Quote\Quotation\Model\Quote
     */
    public function getQuote()
    {
        return $this->coreRegistry->registry('current_quote');
    }

    /**
     * Get ticket from register
     *
     * @return \Cart2Quote\Desk\Model\Ticket|null
     */
    public function getTicket()
    {
        return $this->coreRegistry->registry('ticket_data');
    }

    /**
     * Get Quote Id
     *
     * @return int
     */
    public function getQuoteId()
    {
        return $this->getQuote()->getId();
    }

    /**
     * Set ticket id if set to quote
     *
     * @return int|bool
     */
    public function setQuoteTicketId()
    {
        $quoteId = $this->getQuoteId();

        if ($quoteId) {
            if (isset($this->ticket)) {
                $this->ticket->updateData($this->ticketRepositoryInterface->getByQuoteId($quoteId));
                if ($this->ticket->getId()) {
                    $this->coreRegistry->register('ticket_data', $this->ticket);

                    return $this->ticket->getId();
                }
            }
        }

        return false;
    }

    /**
     * Retrieve options for 'Add Product' split button
     *
     * @param int $ticketId
     * @return array
     */
    protected function getAddProductButtonOptions($ticketId)
    {
        $splitButtonOptions = [];

        foreach ($this->statusCollection->toOptionArray() as $statusId => $status) {
            $onclick =
                "document.getElementById('ticket_form').action = " .
                "'{$this->getTicketSubmitUrl($ticketId, $status['value'])}';".
                " document.getElementById('ticket_form').submit();";

            $splitButtonOptions[$statusId] = [
                'label' => __("Submit as %1", ucfirst($status['label'])),
                'onclick' => $onclick,
                'default' => \Magento\Catalog\Model\Product\Type::DEFAULT_TYPE == $status['value'],
            ];
        }

        return $splitButtonOptions;
    }

    /**
     * Get the Ticket submit url by specified status type
     *
     * @param int $ticketId
     * @param int $statusId
     * @return string
     */
    protected function getTicketSubmitUrl($ticketId, $statusId)
    {
        return $this->getUrl(
            'desk/ticket/quoteticketsave',
            ['id' => $ticketId, 'status_id' => $statusId]
        );
    }

    /**
     * Add the submit button
     *
     * @param int $ticketId
     *
     * @return void
     */
    protected function addSubmitButton($ticketId)
    {
        $statusHtml = __("Open");
        if ($this->ticket->getStatus()) {
            $statusHtml = $this->getLabelHtml($this->ticket->getStatus());
        }

        $addButtonProps = [
            'id' => 'submit_ticket',
            'label' => __("Submit as %1", $statusHtml),
            'class' => 'add',
            'button_class' => '',
            'class_name' => 'Cart2Quote\Desk\Block\Adminhtml\Edit\Overwrite\SplitButton',
            'options' => $this->getAddProductButtonOptions($ticketId),
        ];
        $this->buttonList->add('add_new', $addButtonProps);
    }

    /**
     * Return label formatted for HTML
     *
     * @param string $label The label
     * @return string
     */
    public function getLabelHtml($label)
    {
        return $this->escapeHtml(ucfirst($label));
    }
}
