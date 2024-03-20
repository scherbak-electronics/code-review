<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Downloads\Api\Data\File;

/**
 * @codeCoverageIgnore
 * @api
 * @since 100.0.2
 */
interface ContentUploaderInterface
{
    /**
     * Upload provided downloadable file content
     *
     * @param ContentInterface $fileContent
     * @return array
     * @throws \InvalidArgumentException
     */
    public function upload(ContentInterface $fileContent);
}
