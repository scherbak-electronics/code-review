<?php
/**
 * Copyright (c) 2019. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\AutoProposal\Block\Adminhtml\Form\Field;

/**
 * Class Checkbox
 *
 * @package Cart2Quote\AutoProposal\Block\Adminhtml\Form\Field
 */
class Checkbox extends \Magento\Framework\View\Element\AbstractBlock
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
        $onClick = $this->getOnclick() ? sprintf('onclick="%s"', $this->getOnclick()) : '';

        return sprintf(
            '<input type="checkbox" id="%s" name="%s" value="1" <%%- %s %%> %s class="%s" %s %s/>',
            $inputId,
            $inputName,
            $columnName,
            $size,
            $class,
            $style,
            $onClick
        );
    }
}
