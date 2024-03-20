<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\Downloads\Setup\Patch\Schema;

use Magento\Framework\Setup\Patch\PatchVersionInterface;
use Magento\Framework\Setup\Patch\SchemaPatchInterface;

class MoveLocaleData implements SchemaPatchInterface, PatchVersionInterface
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
        if ($this->moduleDataSetup->getConnection()->tableColumnExists(
            $this->moduleDataSetup->getTable('mageworx_downloads_section'),
            'name'
        )) {
            $insertSelect = $this->moduleDataSetup->getConnection()->insertFromSelect(
                $this->moduleDataSetup->getConnection()
                      ->select()
                      ->from(
                          $this->moduleDataSetup->getTable('mageworx_downloads_section'),
                          ['section_id', new \Zend_Db_Expr('0'), 'name', 'description']
                      ),
                $this->moduleDataSetup->getTable('mageworx_downloads_section_locale'),
                ['section_id', 'store_id', 'name', 'description']
            );

            $this->moduleDataSetup->getConnection()->query($insertSelect);

            $this->moduleDataSetup->getConnection()->dropColumn(
                $this->moduleDataSetup->getTable('mageworx_downloads_section'),
                'name'
            );

            $this->moduleDataSetup->getConnection()->dropColumn(
                $this->moduleDataSetup->getTable('mageworx_downloads_section'),
                'description'
            );
        }


        if ($this->moduleDataSetup->getConnection()->tableColumnExists(
            $this->moduleDataSetup->getTable('mageworx_downloads_attachment'),
            'name'
        )) {
            $insertSelect = $this->moduleDataSetup->getConnection()->insertFromSelect(
                $this->moduleDataSetup->getConnection()
                                      ->select()
                                      ->from(
                                          $this->moduleDataSetup->getTable('mageworx_downloads_attachment'),
                                          ['attachment_id', new \Zend_Db_Expr('0'), 'name', 'description']
                                      ),
                $this->moduleDataSetup->getTable('mageworx_downloads_attachment_locale'),
                ['attachment_id', 'store_id', 'name', 'description']
            );

            $this->moduleDataSetup->getConnection()->query($insertSelect);

            $this->moduleDataSetup->getConnection()->dropColumn(
                $this->moduleDataSetup->getTable('mageworx_downloads_attachment'),
                'name'
            );

            $this->moduleDataSetup->getConnection()->dropColumn(
                $this->moduleDataSetup->getTable('mageworx_downloads_attachment'),
                'description'
            );
        }
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
        return '2.1.5';
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }
}
