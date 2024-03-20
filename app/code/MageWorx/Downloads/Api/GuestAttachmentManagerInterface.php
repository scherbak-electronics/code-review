<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\Downloads\Api;

interface GuestAttachmentManagerInterface
{
    /**
     * @param int $productId
     * @return \MageWorx\Downloads\Api\Data\AttachmentLinkInterface[]
     */
    public function getByProductId(int $productId): array;
}
