<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Downloads\Model;

/**
 * Downloadable component interface
 *
 * @api
 */
interface ComponentInterface
{
    /**
     * Retrieve Base files path
     *
     * @return string
     */
    public function getBasePath();

    /**
     * Retrieve base temporary path
     *
     * @return string
     */
    public function getBaseTmpPath();
}
