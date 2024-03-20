<?php
namespace Customerprice\Attachment\Model;

use Magento\Framework\Data\OptionSourceInterface;
use Customerprice\Attachment\Block\Frontend\FileData;

class Pricelist implements OptionSourceInterface
{
    protected $pricelistType;

    public function __construct(
        FileData $pricelistType
    )
    {
        $this->pricelistType = $pricelistType;
    }

    public function getOptionArrayPrice()
    {
        $options = ['Master Price List' => $this->pricelistType->getMasterValue() ,'Individual Price List' => $this->pricelistType->getIndividualValue() ];
        return $options;
    }

    public function getAllOptions()
    {
        $res = $this->getOptions();
        array_unshift($res, ['value' => '', 'label' => '']);
        return $res;
    }

    public function getOptions()
    {
        $res = [];
        foreach ($this->getOptionArrayPrice() as $index => $value) {
            $res[] = ['value' => $index, 'label' => $value];
        }
        return $res;
    }

    public function toOptionArray()
    {
        return $this->getOptions();
    }
}
