<?php
namespace WPRengine;

/** @var \Herbert\Framework\Shortcode $shortcode */

$shortcode->add(
    'WPRengineForm',
    __NAMESPACE__ . '\Controllers\FrontController@showReservationForm',
    [
    	'group'			 => 'group', 
    	'hide_locations' => 'locations',
    	'type'	  		 => 'type'
    ]
);

$shortcode->add(
	'wp-rengine-search-results',
	__NAMESPACE__ . '\Controllers\FrontController@wpRengineSearchResultsShortcode'
);

$shortcode->add(
	'wp-rengine-car-driver-details',
	__NAMESPACE__ . '\Controllers\FrontController@wpRengineCarAndDriverDetailsShortcode'
);

$shortcode->add(
	'wp-rengine-order-revision',
	__NAMESPACE__ . '\Controllers\FrontController@wpRengineOrderRevisionShortcode'
);

$shortcode->add(
	'wp-rengine-payment',
	__NAMESPACE__ . '\Controllers\FrontController@wpRenginePaymentShortcode'
);