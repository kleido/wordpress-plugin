<?php
namespace WPRengine;
/** @var \Herbert\Framework\Enqueue $enqueue */

// CSS
// ADMIN
$enqueue->admin([
	'as'     => 'adminbootstrapCSS',
	'src'    => Helper::assetUrl('/css/wp-rengine-admin-bootstrap.css'),
	'filter' => ['panel' => '*'],
]);

$enqueue->admin([
	'as'     => 'admincustomCSS',
	'src'    => Helper::assetUrl('/css/wp-rengine-admin.css'),
	'filter' => ['panel' => '*'],
]);

$enqueue->admin([
	'as'     => 'customAdminJS',
	'src'    => Helper::assetUrl('/js/custom-admin.js'),
	'filter' => ['panel' => '*'],
], 'footer');

// wp_enqueue_script("jquery");

// $enqueue->front([
// 	'as'     => 'jquery',
// 	'src'    => 'https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js',
// ], 'header');

// wp_enqueue_script('jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js', array("jquery"));
wp_enqueue_script('jquery');


$enqueue->front([
	'as'     => 'calendarJs',
	'src'    => Helper::assetUrl('/js/bower_components/moment/min/moment.min.js'),
], 'footer');


$enqueue->front([
	'as'     => 'calendarCss',
	'src'    => Helper::assetUrl('/js/bower_components/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css'),
], 'header');


$enqueue->front([
	'as'     => 'calendarjs1',
	'src'    => Helper::assetUrl('/js/bower_components/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js'),
], 'footer');

if (!wp_script_is('bootstrap.min.css', 'enqueued')) {
	$enqueue->front([
		'as'     => 'wp-rengine-front-css2',
		'src'    => 'https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css',
	], 'header');
}
if (!wp_script_is('bootstrap.min.js', 'enqueued')) {
	$enqueue->front([
		'as'     => 'wp-rengine-front-css3',
		'src'    => 'https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js',
	], 'header');
}

$enqueue->front([
	'as'     => 'wp-rengine-front-css',
	'src'    => Helper::assetUrl('/css/wp-rengine-front.css'),
], 'header');

$enqueue->front([
	'as'     => 'wp-rengine-font-awesome-css',
	'src'    => 'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css',
], 'header');

$enqueue->front([
	'as'     => 'flatpickr-css',
	'src'    => 'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css',
], 'header');

$enqueue->front([
	'as'     => 'wp-rengine-front-js',
	'src'    => Helper::assetUrl('/js/custom-front.js'),
], 'header');

$enqueue->front([
	'as'     => 'validator',
	'src'    => Helper::assetUrl('/js/validator.min.js'),
], 'footer');

$enqueue->front([
	'as'     => 'flatpickr-js',
	'src'    => 'https://cdn.jsdelivr.net/npm/flatpickr',
], 'footer');


$enqueue->front([
	'as'     => 'wp-google-map',
	'src'    => Helper::assetUrl('/js/gmaps-init.js'),
], 'footer'); 

$enqueue->front([
	'as'     => 'element-quesries',
	'src'    => 'https://cdnjs.cloudflare.com/ajax/libs/eqcss/1.9.1/EQCSS.min.js',
], 'footer');




