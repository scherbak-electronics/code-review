<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Blog\Model\Source\Post;

/**
 * Post Status source model
 * @package Aheadworks\Blog\Model\Source\Post
 */
class Status implements \Magento\Framework\Option\ArrayInterface
{
    // Statuses to store in DB
    const DRAFT = 'draft';
    const PUBLICATION = 'publication';

    // Statuses to use only in post grid and in post form
    const PUBLICATION_PUBLISHED = 'published';
    const PUBLICATION_SCHEDULED = 'scheduled';

    /**
     * @var array
     */
    private $options;

    /**
     * @return array
     */
    public function getOptions()
    {
        return [
            self::DRAFT => __('Draft'),
            self::PUBLICATION_SCHEDULED => __('Scheduled'),
            self::PUBLICATION_PUBLISHED => __('Published')
        ];
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        if ($this->options === null) {
            $this->options = [];
            foreach ($this->getOptions() as $value => $label) {
                $this->options[] = ['value' => $value, 'label' => $label];
            }
        }
        return $this->options;
    }
}
