<?php
/**
 * Copyright © Pronko Consulting
 * See LICENSE for license details.
 */

namespace Pronko\Elavon\Block\Adminhtml\System\Config;

use Magento\Config\Block\System\Config\Form\Fieldset as FormFieldset;

/**
 * Class Fieldset
 * @package     Pronko\Elavon\Block\Adminhtml\System\Config
 */
class Fieldset extends FormFieldset
{
    /**
     * @var \Magento\Config\Model\Config
     */
    private $backendConfig;

    /**
     * @param \Magento\Backend\Block\Context $context
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Magento\Framework\View\Helper\Js $jsHelper
     * @param \Magento\Config\Model\Config $backendConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Framework\View\Helper\Js $jsHelper,
        \Magento\Config\Model\Config $backendConfig,
        array $data = []
    ) {
        $this->backendConfig = $backendConfig;
        parent::__construct($context, $authSession, $jsHelper, $data);
    }

    /**
     * Add custom css class
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getFrontendClass($element) // @codingStandardsIgnoreLine MEQP2.PHP.ProtectedClassMember.FoundProtected
    {
        $enabledString = $this->_isPaymentEnabled($element) ? ' enabled' : '';
        return parent::_getFrontendClass($element) . ' with-button' . $enabledString;
    }

    /**
     * Check whether current payment method is enabled
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return bool
     */
    private function _isPaymentEnabled($element)
    {
        $groupConfig = $element->getGroup();
        $activityPath = isset($groupConfig['activity_path']) ? $groupConfig['activity_path'] : false;

        return (bool)(string)$this->backendConfig->getConfigDataValue($activityPath);
    }

    /**
     * Return header title part of html for payment solution
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function _getHeaderTitleHtml($element) // @codingStandardsIgnoreLine MEQP2.PHP.ProtectedClassMember.FoundProtected
    {
        $html = '<div class="config-heading" ><div class="heading"><strong>' . $element->getLegend();
        $html .= '</strong>';

        if ($element->getComment()) {
            $html .= '<span class="heading-intro">' . $element->getComment() . '</span>';
        }
        $html .= '</div>';

        $disabledAttributeString = $this->_isPaymentEnabled($element) ? '' : ' disabled="disabled"';
        $disabledClassString = $this->_isPaymentEnabled($element) ? '' : ' disabled';
        $htmlId = $element->getHtmlId();
        $html .= '<div class="button-container"><button type="button" ' .
            $disabledAttributeString .
            ' class="button elavon-action action-configure' .
            $disabledClassString .
            '" id="' .
            $htmlId .
            '-head" ' .
            'data-html-id="' . $htmlId . '" ' .
            'data-toggle-url="' . $this->getUrl('*/*/state') . '"' .
            '"><span class="state-closed">' . __(
                'Configure'
            ) . '</span><span class="state-opened">' . __(
                'Close'
            ) . '</span></button>';

        $groupConfig = $element->getGroup();
        if (!empty($groupConfig['more_url'])) {
            $html .= sprintf(
                '<a class="link-more" href="%s" target="_blank">%s</a>',
                $groupConfig['more_url'],
                __('Learn More')
            );
        }

        $html .= '</div></div>';

        return $html;
    }
}
