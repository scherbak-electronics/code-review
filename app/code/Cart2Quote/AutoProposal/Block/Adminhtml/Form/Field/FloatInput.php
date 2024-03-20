<?php
/**
 * Copyright (c) 2019. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\AutoProposal\Block\Adminhtml\Form\Field;

/**
 * Class FloatInput
 *
 * @package Cart2Quote\AutoProposal\Block\Adminhtml\Form\Field
 */
class FloatInput extends \Magento\Framework\View\Element\AbstractBlock
{
    /**
     * @return string
     */
    protected function _toHtml()
    {
        $column = $this->getColumn();
        $columnName = $this->getColumnName();
        $inputName = $this->getInputName();
        $inputId = $this->getInputId();
        $size = $column['size'] ? sprintf('size="%s"', $column['size']) : '';
        $class = isset($column['class']) ? $column['class'] : 'input-text';
        $style = isset($column['style']) ? sprintf('style="%s"', $column['style']) : '';
        $disabled = $this->getDisabled() ? 'disabled="disabled"' : '';
        return sprintf(
            '<input type="number" step="0.01" id="%s" name="%s" value="<%%- %s %%>" %s class="%s" %s %s/>',
            $inputId,
            $inputName,
            $columnName,
            $size,
            $class,
            $style,
            $disabled
        );
    }
}
