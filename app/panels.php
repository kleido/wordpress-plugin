<?php
namespace WPRengine;

/** @var \Herbert\Framework\Panel $panel */

$panel->add([
	'type'   => 'panel',
	'as'     => 'mainPanel',
	'title'  => 'Workadu',
	'slug'   => 'wp-rengine-get-started',
	'rename' => 'Getting started',
	'icon'   =>  Helper::assetUrl('/img/ResEngineLogoWordpress.svg'),
	'uses'   => __NAMESPACE__ . '\Controllers\AdminController@getstarted',
]);

$panel->add([
	'type'   => 'sub-panel',
	'parent' => 'mainPanel',
	'as'	 => 'wp-rengine-shortcodes',
	'title'  => 'Shortcodes',
	'slug'   => 'wp-rengine-shortcodes',
	'uses'   => __NAMESPACE__ . '\Controllers\AdminController@shortcodes',
]);

$panel->add([
	'type'   => 'sub-panel',
	'parent' => 'mainPanel',
	'as'	 => 'wp-rengine-configuration',
	'title'  => 'Settings',
	'slug'   => 'wp-rengine-configuration',
	'uses'   => __NAMESPACE__ . '\Controllers\AdminController@configuration',
]);

$panel->add([
	'type'   => 'sub-panel',
	'parent' => 'mainPanel',
	'as'     => 'wp-rengine-payments',
	'title'  => 'Payment Integrations',
	'slug'   => 'wp-rengine-payments',
	'uses'   => __NAMESPACE__ . '\Controllers\AdminController@payments',
]);

$panel->add([
	'type'   => 'sub-panel',
	'parent' => 'mainPanel',
	'as'     => 'wp-rengine-places',
	'title'  => 'Locations',
	'slug'   => 'wp-rengine-places',
	'uses'   => __NAMESPACE__ . '\Controllers\AdminController@places',
]);

$panel->add([
	'type'   => 'sub-panel',
	'parent' => 'mainPanel',
	'as'     => 'wp-rengine-api-key',
	'title'  => '<span style="color:#ff007f;"><strong>Get your API Key</strong></span>',
	'slug'   => 'wp-rengine-api-key',
	'uses'   => __NAMESPACE__ . '\Controllers\AdminController@apikey',
]);

