<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace MageWorx\Downloads\Model;

use MageWorx\Downloads\Api\AttachmentManagerInterface;

class GuestAttachmentManager implements \MageWorx\Downloads\Api\GuestAttachmentManagerInterface
{
    /**
     * @var AttachmentManagerInterface
     */
    protected $attachmentManager;

    /**
     * @param AttachmentManagerInterface $attachmentManager
     */
    public function __construct(
        \MageWorx\Downloads\Api\AttachmentManagerInterface $attachmentManager
    ) {
        $this->attachmentManager = $attachmentManager;
    }

    /**
     * @param int $productId
     * @return array|\MageWorx\Downloads\Api\Data\AttachmentInterface[]
     */
    public function getByProductId(int $productId): array
    {
        return $this->attachmentManager->findByProductId($productId);
    }
}
