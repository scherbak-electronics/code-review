<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-search-ultimate
 * @version   2.0.44
 * @copyright Copyright (C) 2021 Mirasvit (https://mirasvit.com/)
 */


namespace Mirasvit\SearchMysql\SearchAdapter\Aggregation;

use Magento\Framework\DB\Select;
use Magento\Framework\Search\Dynamic\IntervalInterface;

class Interval implements IntervalInterface
{
    /**
     * Minimal possible value
     */
    const DELTA = 0.005;

    private $select;

    public function __construct(
        Select $select
    ) {
        $this->select = $select;
    }

    private function getValueFiled(): string
    {
        $field = $this->select->getPart(Select::COLUMNS)[0];

        return $field[1];
    }

   /**
     * @inheritdoc
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function load($limit, $offset = null, $lower = null, $upper = null)
    {
        $select = clone $this->select;
        $value = $this->getValueFiled();
        if ($lower !== null) {
            $select->where("${value} >= ?", $lower - self::DELTA);
        }
        if ($upper !== null) {
            $select->where("${value} < ?", $upper - self::DELTA);
        }
        $select->order("value ASC")
            ->limit($limit, $offset);

        return $this->arrayValuesToFloat(
            $this->select->getConnection()
                ->fetchCol($select)
        );
    }

   /**
     * @inheritdoc
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function loadPrevious($data, $index, $lower = null)
    {
        $select = clone $this->select;
        $value = $this->getValueFiled();
        $select->columns(['count' => 'COUNT(*)'])
            ->where("${value} <  ?", $data - self::DELTA);
        if ($lower !== null) {
            $select->where("${value} >= ?", $lower - self::DELTA);
        }
        $offset = $this->select->getConnection()
            ->fetchRow($select)['count'];
        if (!$offset) {
            return false;
        }

        return $this->load($index - $offset + 1, $offset - 1, $lower);
    }

    /**
     * @inheritdoc
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function loadNext($data, $rightIndex, $upper = null)
    {
        $select = clone $this->select;
        $value = $this->getValueFiled();
        $select->columns(['count' => 'COUNT(*)'])
            ->where("${value} > ?", $data + self::DELTA);

        if ($upper !== null) {
            $select->where("${value} < ? ", $data - self::DELTA);
        }

        $offset = $this->select->getConnection()
            ->fetchRow($select)['count'];

        if (!$offset) {
            return false;
        }

        $select = clone $this->select;
        $select->where("${value} >= ?", $data - self::DELTA);
        if ($upper !== null) {
            $select->where("${value} < ? ", $data - self::DELTA);
        }
        $select->order("${value} DESC")
            ->limit($rightIndex - $offset + 1, $offset - 1);

        return $this->arrayValuesToFloat(
            array_reverse(
                $this->select->getConnection()
                    ->fetchCol($select)
            )
        );
    }

    /**
     * Convert array values to float.
     */
    private function arrayValuesToFloat(array $prices): array
    {
        $returnPrices = [];
        if (is_array($prices) && !empty($prices)) {
            $returnPrices = array_map('floatval', $prices);
        }

        return $returnPrices;
    }
}
