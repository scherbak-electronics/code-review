<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_GroupedProductOption
 * @author     Extension Team
 * @copyright  Copyright (c) 2021 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\GroupedProductOption\Override\Model\ResourceModel\Product;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\ResourceModel\Product\Relation;
use Magento\Framework\EntityManager\MetadataPool;

class Link extends \Magento\GroupedProduct\Model\ResourceModel\Product\Link
{
    /**
     * @var \Bss\GroupedProductOption\Helper\Data
     */
    protected $helper;

    /**
     * Link constructor
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param Relation $catalogProductRelation
     * @param MetadataPool $metadataPool
     * @param \Bss\GroupedProductOption\Helper\Data $helper
     * @param null $connectionName
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        Relation $catalogProductRelation,
        MetadataPool $metadataPool,
        \Bss\GroupedProductOption\Helper\Data $helper,
        $connectionName = null
    ) {
        $this->helper = $helper;
        parent::__construct($context, $catalogProductRelation, $metadataPool, $connectionName);
    }

    /**
     * Retrieve Required children ids
     * Return grouped array, ex array(
     *   group => array(ids)
     * )
     *
     * @param int $parentId
     * @param int $typeId
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getChildrenIds($parentId, $typeId)
    {
        if (!$this->helper->getConfig()) {
            return parent::getChildrenIds($parentId, $typeId);
        }

        $column = $this->helper->isCommunityEdition() ? 'entity_id' : 'row_id';
        $connection = $this->getConnection();
        $childrenIds = [];
        $bind = [':product_id' => (int)$parentId, ':link_type_id' => (int)$typeId];
        $select = $connection->select()->from(
            ['l' => $this->getMainTable()],
            ['linked_product_id']
        )->join(
            ['cpe' => $this->getTable('catalog_product_entity')],
            sprintf(
                'cpe.%s = l.product_id',
                $this->metadataPool->getMetadata(ProductInterface::class)->getLinkField()
            )
        )->where(
            'cpe.' . $column . ' = :product_id'
        )->where(
            'link_type_id = :link_type_id'
        );

        $childrenIds[$typeId] = [];
        $result = $connection->fetchAll($select, $bind);
        foreach ($result as $row) {
            $childrenIds[$typeId][$row['linked_product_id']] = $row['linked_product_id'];
        }

        return $childrenIds;
    }
}
