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



namespace Mirasvit\Search\Api\Data\Index;

use Mirasvit\Search\Api\Data\IndexInterface;

interface InstantProviderInterface
{
    const INSTANT_KEY = '_instant';

    public function setIndex(IndexInterface $index): InstantProviderInterface;

    public function getItems(int $storeId, int $limit): array;

    public function getSize(int $storeId): int;

    public function map(array $documentData, int $storeId): array;
}
