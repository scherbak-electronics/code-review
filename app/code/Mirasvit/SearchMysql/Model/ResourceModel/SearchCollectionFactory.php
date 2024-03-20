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

namespace Mirasvit\SearchMysql\Model\ResourceModel;

use Magento\Framework\App\ObjectManager;

class SearchCollectionFactory
{
    public function create()
    {
        return ObjectManager::getInstance()->create(\Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection::class);
    }
}