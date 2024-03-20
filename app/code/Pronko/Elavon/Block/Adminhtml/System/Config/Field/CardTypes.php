<?php
/**
 * Copyright © Pronko Consulting
 * See LICENSE for license details.
 */

namespace Pronko\Elavon\Block\Adminhtml\System\Config\Field;

use Magento\Framework\View\Element\Context;
use Magento\Framework\View\Element\Html\Select;
use Pronko\Elavon\Source\CcTypeProvider;

/**
 * Class CardTypes
 */
class CardTypes extends Select
{
    /**
     * @var CcTypeProvider
     */
    private $ccTypeProvider;

    /**
     * CardTypes constructor.
     * @param Context $context
     * @param CcTypeProvider $ccTypeProvider
     * @param array $data
     */
    public function __construct(
        Context $context,
        CcTypeProvider $ccTypeProvider,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->ccTypeProvider = $ccTypeProvider;
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml() // @codingStandardsIgnoreLine MEQP2.PHP.ProtectedClassMember.FoundProtected
    {
        if (!$this->getOptions()) {
            $this->setOptions($this->ccTypeProvider->toOptionArray());
        }
        $this->setClass('cc-type-select');
        $this->setExtraParams('multiple="multiple"');
        return parent::_toHtml();
    }

    /**
     * Sets name for input element
     *
     * @param string $value
     * @return $this
     */
    public function setInputName($value)
    {
        return $this->setName($value . '[]');
    }
}
