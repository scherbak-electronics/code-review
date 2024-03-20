<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_ImageGallery
 * @author    Webkul
 * @copyright Copyright (c) 2010-2016 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\ImageGallery\Api\Data;

interface GroupsInterface
{
    /**
    * Constants for keys of data array. Identical to the name of the getter in snake case
    */
    const ENTITY_ID    = 'id';
    /***/

    /**
    * Get ID
    *
    * @return int|null
    */
    public function getId();

    /**
    * Set ID
    *
    * @param int $id
    *
    * @return \Webkul\ImageGallery\Api\Data\GroupsInterface
    */
    public function setId($id);
}
