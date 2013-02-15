<?php
$xpdo_meta_map['pxtCatalog']= array (
  'package' => 'profilext',
  'version' => '1.1',
  'table' => 'catalog',
  'fields' => 
  array (
    'name' => '',
  ),
  'fieldMeta' => 
  array (
    'name' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '55',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
      'index' => 'unique',
    ),
  ),
  'indexes' => 
  array (
    'name' => 
    array (
      'alias' => 'name',
      'primary' => false,
      'unique' => true,
      'type' => 'BTREE',
      'columns' => 
      array (
        'name' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
  ),
  'composites' => 
  array (
    'pxtValues' => 
    array (
      'class' => 'pxtValues',
      'local' => 'id',
      'foreign' => 'pxt_id',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
  ),
);
