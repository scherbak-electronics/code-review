<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Blog\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Post resource model
 * @package Aheadworks\Blog\Model\ResourceModel
 */
class Post extends AbstractDb
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('aw_blog_post', 'id');
    }
}
