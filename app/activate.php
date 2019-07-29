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

$slug = 'workadu-form';
$args = [
	'name'        => $slug,
	'post_type'   => 'page',
	'post_status' => 'publish',
	'numberposts' => 1,
];
$thePage = get_posts($args);

$searchPageParams = [
	'ID'           => ($thePage) ? $thePage[0]->ID : '',
	'post_content' => '[WorkaduForm]',
	'post_title'   => 'Workadu - Form',
	'post_status'  => 'publish',
	'post_type'    => 'page',
	'post_name'    => 'workadu-form',
];
$result = wp_insert_post($searchPageParams, true);
if ($result == 0 || get_class($result) == 'WP_Error') {
	Notifier::error('Workadu could not create the search results page.');
} else {
	Notifier::success('Workadu results page was successfully created.');
}

// 1 - the search page

$slug = 'workadu-search-results';
$args = [
	'name'        => $slug,
	'post_type'   => 'page',
	'post_status' => 'publish',
	'numberposts' => 1,
];
$thePage = get_posts($args);

$searchPageParams = [
	'ID'           => ($thePage) ? $thePage[0]->ID : '',
	'post_content' => '[Workadu-search-results]',
	'post_title'   => 'Workadu - Search Results',
	'post_status'  => 'publish',
	'post_type'    => 'page',
	'post_name'    => 'workadu-search-results',
];
$result = wp_insert_post($searchPageParams, true);
if ($result == 0 || get_class($result) == 'WP_Error') {
	Notifier::error('Workadu could not create the search results page.');
} else {
	Notifier::success('Workadu results page was successfully created.');
}

// 2 - the car details / extras page

$slug = 'workadu-contact-info';
$args = [
	'name'        => $slug,
	'post_type'   => 'page',
	'post_status' => 'publish',
	'numberposts' => 1,
];
$thePage = get_posts($args);

$carAndDriverDetailsPageParams = [
	'ID'           => ($thePage) ? $thePage[0]->ID : '',
	'post_content' => '[Workadu-contact-info]',
	'post_title'   => 'Workadu - Contact info',
	'post_status'  => 'publish',
	'post_type'    => 'page',
	'post_name'    => 'workadu-contact-info',
];

$result = wp_insert_post($carAndDriverDetailsPageParams, true);
if ($result == 0 || get_class($result) == 'WP_Error') {
	Notifier::error('Workadu could not create the car and driver details page.');
}  else {
	Notifier::success('Workadu car and driver details page was successfully created.');
}

// 3 - the order revision page

$slug = 'workadu-order-revision';
$args = [
	'name'        => $slug,
	'post_type'   => 'page',
	'post_status' => 'publish',
	'numberposts' => 1,
];
$thePage = get_posts($args);

$orderRevisionPageParams = [
	'ID'           => ($thePage) ? $thePage[0]->ID : '',
	'post_content' => '[Workadu-order-revision]',
	'post_title'   => 'Workadu - Order Revision',
	'post_status'  => 'publish',
	'post_type'    => 'page',
	'post_name'    => 'workadu-order-revision',
];

$result = wp_insert_post($orderRevisionPageParams, true);
if ($result == 0 || get_class($result) == 'WP_Error') {
	Notifier::error('Workadu could not create the order revision page.');
}  else {
	Notifier::success('Workadu order revision page was successfully created.');
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
	'post_content' => '[Workadu-payment]',
	'post_title'   => 'Workadu - Payment Result',
	'post_status'  => 'publish',
	'post_type'    => 'page',
	'post_name'    => 'workadu-payment',
];

$result = wp_insert_post($paymentPageParams, true);
if ($result == 0 || get_class($result) == 'WP_Error') {
	Notifier::error('Workadu could not create the payment page.');
} else {
	Notifier::success('The Workadu payment page was successfully created.');
}


