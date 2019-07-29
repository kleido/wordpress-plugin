<?php
namespace WPRengine;

/** @var \Herbert\Framework\Shortcode $shortcode */

$shortcode->add(
    'WPRengineForm',
    __NAMESPACE__ . '\Controllers\FrontController@showReservationForm',
    [
    	'service'		 => 'service',
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
	'wp-rengine-contact-info',
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



$shortcode->add(
    'WorkaduForm',
    __NAMESPACE__ . '\Controllers\FrontController@showReservationForm',
    [
    	'services'		 => 'services',
    	'group'			 => 'group', 
    	'hide_locations' => 'locations',
    	'type'	  		 => 'type'
    ]
);

$shortcode->add(
    'WorkaduWidget',
    __NAMESPACE__ . '\Controllers\FrontController@showWidgetForm',
    [
    	'services'		 	=> 'services',
    	'type'	  		 	=> 'type',
    	'locations' 	 	=> 'locations' ,
    	'form'      	 	=> 'form',
    	'background' 	 	=> 'background', 	 
        'buttonBackground' 	=> 'buttonBackground',
        'buttonColor' 	 	=> 'buttonColor', 	 
        'buttonBorderColor' => 'buttonBorderColor',
        'buttonText' 		=> 'buttonText', 		
        'greetingText' 	 	=> 'greetingText', 	 
        'locale' 		 	=> 'locale',		 
        'question' 		 	=> 'question', 		 
    ]
);

$shortcode->add(
	'Workadu-search-results',
	__NAMESPACE__ . '\Controllers\FrontController@wpRengineSearchResultsShortcode'
);


$shortcode->add(
	'Workadu-contact-info',
	__NAMESPACE__ . '\Controllers\FrontController@wpRengineCarAndDriverDetailsShortcode'
);

$shortcode->add(
	'Workadu-order-revision',
	__NAMESPACE__ . '\Controllers\FrontController@wpRengineOrderRevisionShortcode'
);

$shortcode->add(
	'Workadu-payment',
	__NAMESPACE__ . '\Controllers\FrontController@wpRenginePaymentShortcode'
);