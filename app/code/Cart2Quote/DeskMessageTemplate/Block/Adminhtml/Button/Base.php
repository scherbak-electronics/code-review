<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\DeskMessageTemplate\Block\Adminhtml\Button;

use Magento\Backend\Block\Widget\Context;
use Cart2Quote\DeskMessageTemplate\Api\MessageRepositoryInterface;

/**
 * Class Base
 * @package Cart2Quote\DeskMessageTemplate\Block\Adminhtml\Button
 */
class Base
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * @var MessageRepositoryInterface
     */
    protected $messageRepository;

    /**
     * Button constructor.
     *
     * @param Context $context
     * @param MessageRepositoryInterface $messageRepository
     */
    public function __construct(
        Context $context,
        MessageRepositoryInterface $messageRepository
    ) {
        $this->context = $context;
        $this->messageRepository = $messageRepository;
    }

    /**
     * Generate url by route and parameters
     *
     * @param   string $route
     * @param   array $params
     * @return  string
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }
}
