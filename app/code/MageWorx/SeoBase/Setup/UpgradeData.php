<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\SeoBase\Setup;

use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Catalog\Setup\CategorySetupFactory;
use Magento\Store\Model\Store;
use MageWorx\SeoBase\Model\HreflangsConfigReader;
use MageWorx\SeoBase\Model\Source\Hreflangs\LanguageCode as LanguageCodeOptions;
use MageWorx\SeoBase\Model\Source\Hreflangs\CountryCode as CountryCodeOptions;

/**
 * Upgrade Data script
 *
 * @codeCoverageIgnore
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * old hreflang settings paths
     */
    const XML_PATH_HREFLANGS_ENABLED                      = 'mageworx_seo/base/hreflangs/enabled';
    const XML_PATH_HREFLANGS_CATEGORY_ENABLED             = 'mageworx_seo/base/hreflangs/enabled_category';
    const XML_PATH_HREFLANGS_PRODUCT_ENABLED              = 'mageworx_seo/base/hreflangs/enabled_product';
    const XML_PATH_HREFLANGS_CMS_ENABLED                  = 'mageworx_seo/base/hreflangs/enabled_cms';
    const XML_PATH_HREFLANGS_LANDINGPAGE_ENABLED          = 'mageworx_seo/base/hreflangs/enabled_landingpage';
    const XML_PATH_HREFLANGS_USE_MAGENTO_LANGUAGE_CODE    = 'mageworx_seo/base/hreflangs/use_magento_lang_code';
    const XML_PATH_HREFLANGS_LANGUAGE_CODE                = 'mageworx_seo/base/hreflangs/lang_code';
    const XML_PATH_HREFLANGS_COUNTRY_CODE_ENABLED         = 'mageworx_seo/base/hreflangs/country_code_enabled';
    const XML_PATH_HREFLANGS_USE_MAGENTO_COUNTRY_CODE     = 'mageworx_seo/base/hreflangs/use_magento_country_code';
    const XML_PATH_HREFLANGS_COUNTRY_CODE                 = 'mageworx_seo/base/hreflangs/country_code';
    const XML_PATH_HREFLANGS_XDEFAULT_STORE_GLOBAL_SCOPE  = 'mageworx_seo/base/hreflangs/x_default_global';
    const XML_PATH_HREFLANGS_XDEFAULT_STORE_WEBSITE_SCOPE = 'mageworx_seo/base/hreflangs/x_default_website';

    /**
     * Category setup factory
     *
     * @var CategorySetupFactory
     */
    private $categorySetupFactory;

    /**
     * @var Json
     */
    private $serializer;

    /**
     * UpgradeData constructor.
     *
     * @param CategorySetupFactory $categorySetupFactory
     * @param Json $serializer
     */
    public function __construct(CategorySetupFactory $categorySetupFactory, Json $serializer)
    {
        $this->categorySetupFactory = $categorySetupFactory;
        $this->serializer           = $serializer;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '2.0.4', '<')) {

            $catalogSetup = $this->categorySetupFactory->create(['setup' => $setup]);
            $catalogSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'meta_robots',
                [
                    'group'            => 'Search Engine Optimization',
                    'type'             => 'varchar',
                    'backend'          => '',
                    'frontend'         => '',
                    'label'            => 'Meta Robots',
                    'input'            => 'select',
                    'class'            => '',
                    'source'           => 'MageWorx\SeoAll\Model\Source\MetaRobots',
                    'global'           => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_STORE,
                    'visible'          => true,
                    'required'         => false,
                    'user_defined'     => false,
                    'default'          => InstallData::META_ROBOTS_DEFAULT_VALUE,
                    'apply_to'         => '',
                    'visible_on_front' => false,
                    'note'             => 'This setting was added by MageWorx SEO Suite'
                ]
            );

            $catalogSetup->addAttribute(
                \Magento\Catalog\Model\Category::ENTITY,
                'meta_robots',
                [
                    'group'            => 'Search Engine Optimization',
                    'type'             => 'varchar',
                    'backend'          => '',
                    'frontend'         => '',
                    'label'            => 'Meta Robots',
                    'input'            => 'select',
                    'class'            => '',
                    'source'           => 'MageWorx\SeoAll\Model\Source\MetaRobots',
                    'global'           => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_STORE,
                    'visible'          => true,
                    'required'         => false,
                    'user_defined'     => false,
                    'default'          => InstallData::META_ROBOTS_DEFAULT_VALUE,
                    'apply_to'         => '',
                    'visible_on_front' => false,
                    'sort_order'       => 9,
                    'note'             => 'This setting was added by MageWorx SEO Suite'
                ]
            );

            $catalogSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'cross_domain_store',
                [
                    'group'            => 'Search Engine Optimization',
                    'type'             => 'int',
                    'backend'          => '',
                    'frontend'         => '',
                    'label'            => 'Cross Domain Store',
                    'input'            => 'select',
                    'class'            => '',
                    'source'           => 'MageWorx\SeoBase\Model\Source\CrossDomainStore',
                    'global'           => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_STORE,
                    'visible'          => true,
                    'required'         => false,
                    'user_defined'     => false,
                    'default'          => '',
                    'apply_to'         => '',
                    'visible_on_front' => false,
                    'note'             => 'This setting was added by MageWorx SEO Suite'
                ]
            );
        }

        if (version_compare($context->getVersion(), '2.0.5', '<')) {
            $this->updateCategoryLnMetaRobotsSetting($setup);
        }

        if (version_compare($context->getVersion(), '2.2.1', '<')) {
            $this->updateHreflangSettings($setup);
        }

        $setup->endSetup();
    }

    /**
     * @param ModuleDataSetupInterface $setup
     */
    private function updateCategoryLnMetaRobotsSetting(ModuleDataSetupInterface $setup)
    {
        $setup->getConnection()->update(
            $setup->getTable('core_config_data'),
            ['path' => 'mageworx_seo/base/robots/category_ln_pages_robots'],
            "path = 'mageworx_seo/base/robots/category_filter_to_noindex'"
        );

        $setup->getConnection()->update(
            $setup->getTable('core_config_data'),
            ['value' => 'NOINDEX, FOLLOW'],
            "path = 'mageworx_seo/base/robots/category_ln_pages_robots' AND value = '1'"
        );

        $setup->getConnection()->update(
            $setup->getTable('core_config_data'),
            ['value' => ''],
            "path = 'mageworx_seo/base/robots/category_ln_pages_robots' AND value = '0'"
        );
    }

    /**
     * @param ModuleDataSetupInterface $setup
     */
    private function updateHreflangSettings(ModuleDataSetupInterface $setup)
    {
        $oldPaths   = [
            self::XML_PATH_HREFLANGS_ENABLED,
            self::XML_PATH_HREFLANGS_CATEGORY_ENABLED,
            self::XML_PATH_HREFLANGS_PRODUCT_ENABLED,
            self::XML_PATH_HREFLANGS_CMS_ENABLED,
            self::XML_PATH_HREFLANGS_LANDINGPAGE_ENABLED,
            self::XML_PATH_HREFLANGS_USE_MAGENTO_LANGUAGE_CODE,
            self::XML_PATH_HREFLANGS_LANGUAGE_CODE,
            self::XML_PATH_HREFLANGS_COUNTRY_CODE_ENABLED,
            self::XML_PATH_HREFLANGS_USE_MAGENTO_COUNTRY_CODE,
            self::XML_PATH_HREFLANGS_COUNTRY_CODE,
            self::XML_PATH_HREFLANGS_XDEFAULT_STORE_GLOBAL_SCOPE,
            self::XML_PATH_HREFLANGS_XDEFAULT_STORE_WEBSITE_SCOPE
        ];
        $paths      = array_merge($oldPaths, [HreflangsConfigReader::XML_PATH_HREFLANGS_SCOPE]);
        $connection = $setup->getConnection();
        $select     = $connection->select();
        $select
            ->from($setup->getTable('core_config_data'), ['scope', 'scope_id', 'path', 'value'])
            ->where('path IN (?)', $paths);

        $groupedConfigData = [];

        foreach ($connection->fetchAll($select) as $row) {
            $groupedConfigData[$row['path']][$row['scope']][$row['scope_id']] = $row['value'];
        }

        if (empty($groupedConfigData)) {
            return;
        }

        if (!empty($groupedConfigData[HreflangsConfigReader::XML_PATH_HREFLANGS_SCOPE])
            && count($groupedConfigData) === 1
        ) {
            return;
        }

        $groupedStoreIds  = $this->getStoreIdsGroupedByWebsite($setup);
        $configData       = $this->prepareHreflangConfigData($groupedConfigData, $groupedStoreIds);
        $hreflangSettings = [];

        foreach ($groupedStoreIds as $storeIds) {
            foreach ($storeIds as $storeId) {
                $data    = [];
                $storeId = (int)$storeId;

                if (empty($configData[self::XML_PATH_HREFLANGS_ENABLED][$storeId])) {
                    continue;
                }

                $data[HreflangsConfigReader::STORE] = $storeId;

                $this->addPagesToStoreHreflangSettingsData($storeId, $data, $configData);
                $this->addLanguageCodeToStoreHreflangSettingsData($storeId, $data, $configData);
                $this->addCountryCodeToStoreHreflangSettingsData($storeId, $data, $configData);
                $this->addXDefaultToStoreHreflangSettingsData($storeId, $data, $configData);

                $hreflangSettings[$storeId] = $data;
            }
        }

        if (!empty($hreflangSettings)) {
            $hreflangSettings = $this->serializer->serialize($hreflangSettings);

            $connection->insertOnDuplicate(
                $setup->getTable('core_config_data'),
                [
                    'scope'    => 'default',
                    'scope_id' => 0,
                    'path'     => HreflangsConfigReader::XML_PATH_HREFLANGS_HREFLANG_SETTINGS,
                    'value'    => $hreflangSettings
                ]
            );
        }

        $connection->delete($setup->getTable('core_config_data'), ['path IN (?)' => $oldPaths]);
    }

    /**
     * @param array $groupedConfigData
     * @param array $groupedStoreIds
     * @return array
     */
    private function prepareHreflangConfigData(array $groupedConfigData, array $groupedStoreIds): array
    {
        $configData     = [];
        $defaultStoreId = Store::DEFAULT_STORE_ID;

        if (!empty($groupedConfigData[HreflangsConfigReader::XML_PATH_HREFLANGS_SCOPE])) {
            $scope = $groupedConfigData[HreflangsConfigReader::XML_PATH_HREFLANGS_SCOPE]['default'][$defaultStoreId];

            $configData[HreflangsConfigReader::XML_PATH_HREFLANGS_SCOPE][$defaultStoreId] = $scope;

            unset($groupedConfigData[HreflangsConfigReader::XML_PATH_HREFLANGS_SCOPE]);
        }

        if (!empty($groupedConfigData[self::XML_PATH_HREFLANGS_XDEFAULT_STORE_GLOBAL_SCOPE])) {
            $xDefault = (string)$groupedConfigData[self::XML_PATH_HREFLANGS_XDEFAULT_STORE_GLOBAL_SCOPE]['default'][0];

            $configData[self::XML_PATH_HREFLANGS_XDEFAULT_STORE_GLOBAL_SCOPE][$defaultStoreId] = $xDefault;

            unset($groupedConfigData[self::XML_PATH_HREFLANGS_XDEFAULT_STORE_GLOBAL_SCOPE]);
        }

        if (!empty($groupedConfigData[self::XML_PATH_HREFLANGS_XDEFAULT_STORE_WEBSITE_SCOPE])) {
            $xDefault = (string)$groupedConfigData[self::XML_PATH_HREFLANGS_XDEFAULT_STORE_WEBSITE_SCOPE]['default'][0];

            $configData[self::XML_PATH_HREFLANGS_XDEFAULT_STORE_WEBSITE_SCOPE][$defaultStoreId] = $xDefault;

            unset($groupedConfigData[self::XML_PATH_HREFLANGS_XDEFAULT_STORE_WEBSITE_SCOPE]);
        }

        foreach ($groupedConfigData as $path => $groupedValues) {
            foreach ($groupedStoreIds as $websiteId => $storeIds) {
                foreach ($storeIds as $storeId) {
                    if (!empty($groupedValues['stores']) && array_key_exists($storeId, $groupedValues['stores'])) {
                        $configData[$path][$storeId] = $groupedValues['stores'][$storeId];
                        continue;
                    }

                    if (!empty($groupedValues['websites'])
                        && array_key_exists($websiteId, $groupedValues['websites'])
                    ) {
                        $configData[$path][$storeId] = $groupedValues['websites'][$websiteId];
                        continue;
                    }

                    if (!empty($groupedValues['default'])) {
                        $configData[$path][$storeId] = $groupedValues['default'][$defaultStoreId];
                    }
                }
            }
        }

        return $configData;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @return array
     */
    private function getStoreIdsGroupedByWebsite(ModuleDataSetupInterface $setup): array
    {
        $connection = $setup->getConnection();
        $select     = $connection->select();
        $select
            ->from($setup->getTable('store'), ['store_id', 'website_id'])
            ->where('store_id != ?', Store::DEFAULT_STORE_ID);

        $storeIds = [];

        foreach ($connection->fetchAll($select) as $row) {
            $storeIds[$row['website_id']][] = $row['store_id'];
        }

        return $storeIds;
    }

    /**
     * @param int $storeId
     * @param array $data
     * @param array $oldConfigData
     */
    private function addPagesToStoreHreflangSettingsData(int $storeId, array &$data, array $oldConfigData)
    {
        if (!empty($oldConfigData[self::XML_PATH_HREFLANGS_CATEGORY_ENABLED][$storeId])) {
            $data[HreflangsConfigReader::PAGES][] = 'category';
        }

        if (!empty($oldConfigData[self::XML_PATH_HREFLANGS_PRODUCT_ENABLED][$storeId])) {
            $data[HreflangsConfigReader::PAGES][] = 'product';
        }

        if (!empty($oldConfigData[self::XML_PATH_HREFLANGS_CMS_ENABLED][$storeId])) {
            $data[HreflangsConfigReader::PAGES][] = 'cms';
        }

        if (!empty($oldConfigData[self::XML_PATH_HREFLANGS_LANDINGPAGE_ENABLED][$storeId])) {
            $data[HreflangsConfigReader::PAGES][] = 'landingpage';
        }
    }

    /**
     * @param int $storeId
     * @param array $data
     * @param array $oldConfigData
     */
    private function addLanguageCodeToStoreHreflangSettingsData(int $storeId, array &$data, array $oldConfigData)
    {
        if (empty($oldConfigData[self::XML_PATH_HREFLANGS_USE_MAGENTO_LANGUAGE_CODE][$storeId])) {
            $languageCode = '';

            if (isset($oldConfigData[self::XML_PATH_HREFLANGS_LANGUAGE_CODE][$storeId])) {
                $languageCode = $oldConfigData[self::XML_PATH_HREFLANGS_LANGUAGE_CODE][$storeId];
            }

            $data[HreflangsConfigReader::LANGUAGE_CODE] = $languageCode;
        } else {
            $data[HreflangsConfigReader::LANGUAGE_CODE] = LanguageCodeOptions::USE_CONFIG;
        }
    }

    /**
     * @param int $storeId
     * @param array $data
     * @param array $oldConfigData
     */
    private function addCountryCodeToStoreHreflangSettingsData(int $storeId, array &$data, array $oldConfigData)
    {
        if (empty($oldConfigData[self::XML_PATH_HREFLANGS_COUNTRY_CODE_ENABLED][$storeId])) {
            $data[HreflangsConfigReader::COUNTRY_CODE] = CountryCodeOptions::DO_NOT_ADD;
        } else {
            if (empty($oldConfigData[self::XML_PATH_HREFLANGS_USE_MAGENTO_COUNTRY_CODE][$storeId])) {
                $countryCode = '';

                if (isset($oldConfigData[self::XML_PATH_HREFLANGS_COUNTRY_CODE][$storeId])) {
                    $countryCode = $oldConfigData[self::XML_PATH_HREFLANGS_COUNTRY_CODE][$storeId];
                }

                $data[HreflangsConfigReader::COUNTRY_CODE] = $countryCode;
            } else {
                $data[HreflangsConfigReader::COUNTRY_CODE] = CountryCodeOptions::USE_CONFIG;
            }
        }
    }

    /**
     * @param int $storeId
     * @param array $data
     * @param array $oldConfigData
     */
    private function addXDefaultToStoreHreflangSettingsData(int $storeId, array &$data, array $oldConfigData)
    {
        $defaultStoreId = Store::DEFAULT_STORE_ID;

        if (!empty($oldConfigData[HreflangsConfigReader::XML_PATH_HREFLANGS_SCOPE][$defaultStoreId])) {
            if (!empty($oldConfigData[self::XML_PATH_HREFLANGS_XDEFAULT_STORE_WEBSITE_SCOPE][$defaultStoreId])
            ) {
                $storeIds = $oldConfigData[self::XML_PATH_HREFLANGS_XDEFAULT_STORE_WEBSITE_SCOPE][$defaultStoreId];
                $storeIds = explode(',', $storeIds);

                if (in_array($storeId, $storeIds)) {
                    $data[HreflangsConfigReader::X_DEFAULT] = 1;
                }
            }
        } else {
            if (!empty($oldConfigData[self::XML_PATH_HREFLANGS_XDEFAULT_STORE_GLOBAL_SCOPE][$defaultStoreId])
                && $oldConfigData[self::XML_PATH_HREFLANGS_XDEFAULT_STORE_GLOBAL_SCOPE][$defaultStoreId] == $storeId
            ) {
                $data[HreflangsConfigReader::X_DEFAULT] = 1;
            }
        }
    }
}
