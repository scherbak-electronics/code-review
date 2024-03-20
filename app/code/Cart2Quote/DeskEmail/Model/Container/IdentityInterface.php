<?php
/**
 * Copyright (c) 2019. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\DeskEmail\Model\Container;

use Magento\Store\Model\Store;

/**
 * Interface IdentityInterface
 * @package Cart2Quote\DeskEmail\Model\Container
 */
interface IdentityInterface
{
    /**
     * Check if the email is enabled
     *
     * @return bool
     */
    public function isEnabled();

    /**
     * Get the copy to emails
     *
     * @return array|bool
     */
    public function getEmailCopyTo();

    /**
     * Get the copy method
     *
     * @return string
     */
    public function getCopyMethod();

    /**
     * Get the admin template ID
     *
     * @return string
     */
    public function getAdminTemplateId();

    /**
     * Get the template ID
     *
     * @return string
     */
    public function getTemplateId();

    /**
     * Get the sender email
     *
     * @return string
     */
    public function getEmailIdentity();

    /**
     * Get the main email
     *
     * @return string
     */
    public function getMainEmail();

    /**
     * Get the main name
     *
     * @return string
     */
    public function getMainName();

    /**
     * Get the store
     *
     * @return Store
     */
    public function getStore();

    /**
     * Set the store
     *
     * @param Store $store
     * @return void
     */
    public function setStore(Store $store);

    /**
     * Set the main email
     *
     * @param string $email
     * @return void
     */
    public function setMainEmail($email);

    /**
     * Set the main name
     *
     * @param string $name
     * @return void
     */
    public function setMainName($name);
}
