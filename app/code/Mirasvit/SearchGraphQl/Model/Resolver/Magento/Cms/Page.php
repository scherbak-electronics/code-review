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


declare(strict_types=1);

namespace Mirasvit\SearchGraphQl\Model\Resolver\Magento\Cms;

use Magento\CmsGraphQl\Model\Resolver\DataProvider\Page as PageDataProvider;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class Page implements ResolverInterface
{
    private $pageDataProvider;

    public function __construct(
        PageDataProvider $pageDataProvider
    ) {
        $this->pageDataProvider = $pageDataProvider;
    }

    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        /** @var \Magento\Cms\Model\ResourceModel\Page\Collection $collection */
        $collection = $value['collection'];

        $collection->setPageSize($args['pageSize'])
            ->setCurPage($args['currentPage']);

        $items = [];

        foreach ($collection as $page) {
            $items[] = $this->pageDataProvider->getDataByPageId((int)$page->getId());
        }

        return $items;
    }
}
