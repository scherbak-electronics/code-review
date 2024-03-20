<?php
/**
 * Copyright © Pronko Consulting
 * See LICENSE for license details.
 */

/**
 * Backend for serialized array data
 *
 */
namespace Pronko\Elavon\Model\Config\Backend\Serialized;

class ArraySerialized extends \Pronko\Elavon\Model\Config\Backend\Serialized
{
    /**
     * Unset array element with '__empty' key
     *
     * @return $this
     */
    public function beforeSave()
    {
        $value = $this->getValue();
        if (is_array($value)) {
            unset($value['__empty']);
        }
        $this->setValue($value);
        return parent::beforeSave();
    }
}
