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



namespace Mirasvit\Search\Plugin\Frontend;

use Magento\Search\Model\Query as QueryModel;
use Magento\Search\Model\ResourceModel\Query;

/**
 * @see \Magento\Search\Model\ResourceModel\Query::saveNumResults()
 * @see \Magento\Search\Model\ResourceModel\Query::saveIncrementalPopularity()
 */
class DetectHackAttempts extends Query
{
    private $possibleInjectionTerms
        = [
            'admin', 'wp', 'login', 'db', 'zip', 'rar', 'tar', 'gz', 'sql', '7z', 'bz2', 'bak',
            'bck', 'database', 'sid', 'localhost', 'backup', 'magento', 'config', 'passwd',
            'panel', 'mysql', 'admo', 'ajaxplorer', 'dump', 'select', 'where', 'union', 'teal', 'sweatshirt',
        ];

    public function aroundSaveNumResults(Query $subject, callable $proceed, QueryModel $query): void
    {
        if (!$this->isPossibleInjection()) {
            $proceed($query);
        }
    }

    public function aroundSaveIncrementalPopularity(Query $subject, callable $proceed, QueryModel $query): void
    {
        if (!$this->isPossibleInjection()) {
            $proceed($query);
        }
    }

    private function isPossibleInjection()
    {
        $result = false;
        $term   = filter_input(INPUT_GET, 'q', FILTER_UNSAFE_RAW);
        if (empty($term)) {
            return $result;
        }

        if (preg_match('~' . implode('|', $this->possibleInjectionTerms) . '~', $term)) {
            $result = true;
        } elseif (preg_match('~\.' . implode('|\.', $this->possibleInjectionTerms) . '~', $term)) {
            $result = true;
        } elseif (preg_match('~.*' . implode('|.*', $this->possibleInjectionTerms) . '~', $term)) {
            $result = true;
        } elseif (preg_match(
            "~('(''|[^'])*')|(;)|(\b(ALTER|CREATE|DELETE|DROP|EXEC(UTE){0,1}|INSERT( +INTO){0,1}|MERGE|SELECT|UPDATE|UNION( +ALL){0,1})\b)~i",
            $term)
        ) {
            $result = true;
        }

        return $result;
    }
}
