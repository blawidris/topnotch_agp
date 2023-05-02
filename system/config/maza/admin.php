<?php
// Status
$_['maza_status'] = false;

// Actions
// $_['action_pre_action']  = array(
// 	'startup/startup',
// 	'startup/error',
// 	'startup/event',
// 	'startup/sass',
// 	'startup/login',
// 	'startup/permission',
// 	'extension/maza/startup'
// );

$_['mz_cache_engine'] = 'db'; // File or db

// Action Events
$_['mz_action_event'] = array(
	'model/design/layout/deleteLayout/before' => array(
		'extension/maza/event/design/layout/deleteLayout'
	),
	'model/catalog/category/deleteCategory/before' => array(
		'extension/maza/event/catalog/deleteCategory'
	),
	'model/catalog/manufacturer/deleteManufacturer/before' => array(
		'extension/maza/event/catalog/deleteManufacturer'
	),
	'model/catalog/product/deleteProduct/before' => array(
		'extension/maza/event/catalog/deleteProduct'
	),
	'model/localisation/language/addLanguage/after' => array(
		'extension/maza/event/language/addLanguage'
	),
	'model/localisation/language/deleteLanguage/after' => array(
		'extension/maza/event/language/deleteLanguage'
	),
	'view/common/column_left/before' => array(
		'extension/maza/event/common/menu'
	),
);