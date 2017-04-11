<?php
namespace WPRengine;

/** @var \Herbert\Framework\Panel $panel */

$panel->add([
	'type'   => 'panel',
	'as'     => 'mainPanel',
	'title'  => 'WP R-Engine',
	'slug'   => 'wp-rengine-configuration',
	'rename' => 'Configuration',
	'icon'   =>  Helper::assetUrl('/img/ResEngineLogoWordpress.png'),
	'uses'   => __NAMESPACE__ . '\Controllers\AdminController@configuration',
]);

$panel->add([
	'type'   => 'sub-panel',
	'parent' => 'mainPanel',
	'as'     => 'wp-rengine-places',
	'title'  => 'Locations',
	'slug'   => 'wp-rengine-places',
	'uses'   => __NAMESPACE__ . '\Controllers\AdminController@places',
]);

