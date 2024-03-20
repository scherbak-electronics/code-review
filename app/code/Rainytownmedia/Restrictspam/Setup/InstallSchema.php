<?php 
namespace Rainytownmedia\Restrictspam\Setup;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\DB\Ddl\Table;
class InstallSchema implements \Magento\Framework\Setup\InstallSchemaInterface{
    public function install(SchemaSetupInterface $setup,ModuleContextInterface $context){
        $setup->startSetup();
        $conn = $setup->getConnection();
        $tableName = $setup->getTable('rainytownmedia_log_spam');
        if($conn->isTableExists($tableName) != true){
            $table = $conn->newTable($tableName)
                            ->addColumn(
                                'entity_id',
                                Table::TYPE_INTEGER,
                                null,
								['identity'=>true,'unsigned'=>true,'nullable'=>false,'primary'=>true]
                            )
                            ->addColumn(
                                'customer_id',
                                Table::TYPE_INTEGER,
                                ['unsigned'=>true, 'nullable'=>true,'default'=>'']
                            )
                            ->addColumn(
                                'email',
                                Table::TYPE_TEXT,
                                255,
                                ['nullbale'=>true,'default'=>'']
                            )
							->addColumn(
                                'form_type',
                                Table::TYPE_TEXT,
                                255,
                                ['nullbale'=>true,'default'=>'']
                            )
							->addColumn(
                                'speed',
                                Table::TYPE_INTEGER,
                                null,
                                ['unsigned'=>true, 'nullable'=>true,'default'=>0]
                            )
							->addColumn(
                                'is_spam',
                                Table::TYPE_SMALLINT,
                                1,
                                ['unsigned'=>true, 'nullable'=>true,'default'=>0]
                            )
                            ->setOption('charset','utf8');
            $conn->createTable($table);
        }
        $setup->endSetup();
    }
}
 ?>