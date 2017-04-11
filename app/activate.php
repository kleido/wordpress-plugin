<?php
namespace WPRengine;
use Herbert\Framework\Notifier;

/** @var  \Herbert\Framework\Application $container */
/** @var  \Herbert\Framework\Http $http */
/** @var  \Herbert\Framework\Router $router */
/** @var  \Herbert\Framework\Enqueue $enqueue */
/** @var  \Herbert\Framework\Panel $panel */
/** @var  \Herbert\Framework\Shortcode $shortcode */
/** @var  \Herbert\Framework\Widget $widget */

// we need to create 4 pages

// The form page

$slug = 'wp-rengine-form';
$args = [
	'name'        => $slug,
	'post_type'   => 'page',
	'post_status' => 'publish',
	'numberposts' => 1,
];
$thePage = get_posts($args);

$searchPageParams = [
	'ID'           => ($thePage) ? $thePage[0]->ID : '',
	'post_content' => '[WPRengineForm]',
	'post_title'   => 'WP Rengine - Form',
	'post_status'  => 'publish',
	'post_type'    => 'page',
	'post_name'    => 'wp-rengine-form',
];
$result = wp_insert_post($searchPageParams, true);
if ($result == 0 || get_class($result) == 'WP_Error') {
	Notifier::error('WP Rengine could not create the search results page.');
} else {
	Notifier::success('The WP Rengine results page was successfully created.');
}

// 1 - the search page

$slug = 'wp-rengine-search-results';
$args = [
	'name'        => $slug,
	'post_type'   => 'page',
	'post_status' => 'publish',
	'numberposts' => 1,
];
$thePage = get_posts($args);

$searchPageParams = [
	'ID'           => ($thePage) ? $thePage[0]->ID : '',
	'post_content' => '[wp-rengine-search-results]',
	'post_title'   => 'WP Rengine - Search Results',
	'post_status'  => 'publish',
	'post_type'    => 'page',
	'post_name'    => 'wp-rengine-search-results',
];
$result = wp_insert_post($searchPageParams, true);
if ($result == 0 || get_class($result) == 'WP_Error') {
	Notifier::error('WP Rengine could not create the search results page.');
} else {
	Notifier::success('The WP Rengine results page was successfully created.');
}

// 2 - the car details / extras page

$slug = 'wp-rengine-car-driver-details';
$args = [
	'name'        => $slug,
	'post_type'   => 'page',
	'post_status' => 'publish',
	'numberposts' => 1,
];
$thePage = get_posts($args);

$carAndDriverDetailsPageParams = [
	'ID'           => ($thePage) ? $thePage[0]->ID : '',
	'post_content' => '[wp-rengine-car-driver-details]',
	'post_title'   => 'WP Rengine - Car and Driver details',
	'post_status'  => 'publish',
	'post_type'    => 'page',
	'post_name'    => 'wp-rengine-car-driver-details',
];

$result = wp_insert_post($carAndDriverDetailsPageParams, true);
if ($result == 0 || get_class($result) == 'WP_Error') {
	Notifier::error('WP Rengine could not create the car and driver details page.');
}  else {
	Notifier::success('The WP Rengine car and driver details page was successfully created.');
}

// 3 - the order revision page

$slug = 'wp-rengine-order-revision';
$args = [
	'name'        => $slug,
	'post_type'   => 'page',
	'post_status' => 'publish',
	'numberposts' => 1,
];
$thePage = get_posts($args);

$orderRevisionPageParams = [
	'ID'           => ($thePage) ? $thePage[0]->ID : '',
	'post_content' => '[wp-rengine-order-revision]',
	'post_title'   => 'WP Rengine - Order Revision',
	'post_status'  => 'publish',
	'post_type'    => 'page',
	'post_name'    => 'wp-rengine-order-revision',
];

$result = wp_insert_post($orderRevisionPageParams, true);
if ($result == 0 || get_class($result) == 'WP_Error') {
	Notifier::error('WP Rengine could not create the order revision page.');
}  else {
	Notifier::success('The WP Rengine order revision page was successfully created.');
}

// 4 - the payment results page (success & fail)

$slug = 'wp-rengine-payment';
$args = [
	'name'        => $slug,
	'post_type'   => 'page',
	'post_status' => 'publish',
	'numberposts' => 1,
];
$thePage = get_posts($args);

$paymentPageParams = [
	'ID'           => ($thePage) ? $thePage[0]->ID : '',
	'post_content' => '[wp-rengine-payment]',
	'post_title'   => 'WP Rengine - Payment Result',
	'post_status'  => 'publish',
	'post_type'    => 'page',
	'post_name'    => 'wp-rengine-payment',
];

$result = wp_insert_post($paymentPageParams, true);
if ($result == 0 || get_class($result) == 'WP_Error') {
	Notifier::error('WP Rengine could not create the payment page.');
} else {
	Notifier::success('The WP Rengine payment page was successfully created.');
}
