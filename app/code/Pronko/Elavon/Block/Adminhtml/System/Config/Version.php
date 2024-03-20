<?php
/**
 * Copyright © Pronko Consulting
 * See LICENSE for license details.
 */

namespace Pronko\Elavon\Block\Adminhtml\System\Config;

use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Pronko\Elavon\Spi\ConfigInterface;

/**
 * Backend system config Module Version field renderer
 */
class Version extends Field
{
    /**
     * @var ConfigInterface
     */
    protected $config; // @codingStandardsIgnoreLine MEQP2.PHP.ProtectedClassMember.FoundProtected

    /**
     * Version constructor.
     * @param Context $context
     * @param ConfigInterface $config
     * @param array $data
     */
    public function __construct(
        Context $context,
        ConfigInterface $config,
        array $data = []
    ) {
        $this->config = $config;
        parent::__construct($context, $data);
    }

    /**
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element) // @codingStandardsIgnoreLine MEQP2.PHP.ProtectedClassMember.FoundProtected
    {
        $html = $this->config->getModuleVersion() .
            ' <a href="#" class="changelog elavon popup" id="elavon-changelog">' .
            __('Release Notes') .
            '</a>';

        $element->setData('text', $html);
        return parent::_getElementHtml($element);
    }

    /**
     * @param AbstractElement $element
     * @return string
     */
    protected function _renderScopeLabel(AbstractElement $element) // @codingStandardsIgnoreLine MEQP2.PHP.ProtectedClassMember.FoundProtected
    {
        return '';
    }

    /**
     * @param AbstractElement $element
     * @return string
     */
    protected function _renderInheritCheckbox(AbstractElement $element) // @codingStandardsIgnoreLine MEQP2.PHP.ProtectedClassMember.FoundProtected
    {
        return '';
    }
}
