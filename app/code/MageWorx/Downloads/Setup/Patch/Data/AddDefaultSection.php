<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\Downloads\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchVersionInterface;

class AddDefaultSection implements DataPatchInterface, PatchVersionInterface
{
    /**
     * @var \Magento\Framework\Setup\ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @param \Magento\Framework\Setup\ModuleDataSetupInterface $moduleDataSetup
     */
    public function __construct(
        \Magento\Framework\Setup\ModuleDataSetupInterface $moduleDataSetup
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
    }

    /**
     * {@inheritdoc}
     */
    public function apply()
    {
        /**
         * Install default section
         */
        $bind = [
            'section_id'  => \MageWorx\Downloads\Model\Section::DEFAULT_ID,
            'is_active'   => \MageWorx\Downloads\Model\Section::STATUS_ENABLED
        ];
        $this->moduleDataSetup->getConnection()->insertOnDuplicate(
            $this->moduleDataSetup->getTable('mageworx_downloads_section'),
            $bind
        );

        $bind = [
            'section_id'  => \MageWorx\Downloads\Model\Section::DEFAULT_ID,
            'store_id'    => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
            'name'        => 'Default',
            'description' => 'Default section'
        ];
        $this->moduleDataSetup->getConnection()->insertOnDuplicate(
            $this->moduleDataSetup->getTable('mageworx_downloads_section_locale'),
            $bind
        );
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public static function getVersion()
    {
        return '1.0.0';
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }
}
