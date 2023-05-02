<?php
// Status
$_['maza_status'] = false;

$_['mz_cache_engine'] = 'db'; // File or db

$_['mz_cache_route'] = array(
	'common/home',
	'product/category',
	'product/manufacturer',
	'product/search',
	'product/product',
	'product/special',
	'extension/maza/product/quick_view',
	'extension/maza/blog/article',
	'extension/maza/blog/author',
	'extension/maza/blog/category',
	'extension/maza/blog/home',
);

// Autoload Model
$_['mz_model_autoload']       = array(
	'extension/maza/opencart',
	'extension/maza/theme',
	'extension/maza/skin',
	'extension/maza/common',
	'extension/maza/asset',
	'extension/maza/catalog/product',
	'extension/maza/image',
	'tool/image',
);

// Action Events
$_['mz_action_event'] = array(
	'controller/checkout/checkout/before' => array(
		'extension/maza/event/checkout/checkout/controllerBefore'
	),
	'controller/checkout/cart/add/after' => array(
		'extension/maza/event/checkout/cart/addAfter'
	),
	'controller/account/wishlist/add/after' => array(
		'extension/maza/event/account/wishlist/addAfter'
	),
	'controller/product/compare/add/after' => array(
		'extension/maza/event/product/compare/addAfter'
	),
	'controller/product/product/before' => array(
		'extension/maza/event/recent_viewed/beforeController'
	),
	'controller/extension/maza/blog/article/before' => array(
		'extension/maza/event/recent_viewed/beforeController'
	),
	'model/tool/image/resize/before' => array(
		'extension/maza/event/common/resize'
	),
	'model/catalog/product/getProducts/before' => array(
		'extension/maza/event/common/product/getProducts'
	),
	'model/catalog/product/getTotalProducts/before' => array(
		'extension/maza/event/common/product/getTotalProducts'
	),
	'model/catalog/product/getProductAttributes/before' => array(
		'extension/maza/event/product/compare/attribute'
	),
	'view/common/header/before' => array(
		'extension/maza/event/common/header/index'
	),
	'view/common/footer/before' => array(
		'extension/maza/event/common/footer/index'
	),	
	'view/*/before' => array(
		'extension/maza/event/common/beforeView'
	),
	'view/common/column_left/before' => array(
		'extension/maza/event/layout_builder/default'
	),
	'view/common/column_right/before' => array(
		'extension/maza/event/layout_builder/default'
	),
	'view/common/content_top/before' => array(
		'extension/maza/event/layout_builder/default'
	),
	'view/common/content_bottom/before' => array(
		'extension/maza/event/layout_builder/default'
	),
	
);

// Action hook
$_['mz_action_hook'] = array(
	'gallery' => array(
		'extension/maza/hooks/gallery/default'
	),
);
