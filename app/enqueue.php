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

// JS
// ADMIN
// $enqueue->admin([
// 	'as'     => 'bootstrapJS',
// 	'src'    => Helper::assetUrl('/js/bootstrap.min.js'),
// 	'filter' => ['panel' => '*'],
// ], 'footer');

$enqueue->admin([
	'as'     => 'customAdminJS',
	'src'    => Helper::assetUrl('/js/custom-admin.js'),
	'filter' => ['panel' => '*'],
], 'footer');

$enqueue->front([
	'as'     => 'calendarCss',
	'src'    => Helper::assetUrl('/js/bower_components/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css'),
], 'footer');

$enqueue->front([
	'as'     => 'calendarJs',
	'src'    => Helper::assetUrl('/js/bower_components/moment/min/moment.min.js'),
], 'footer');

$enqueue->front([
	'as'     => 'datetimeJs',
	'src'    => Helper::assetUrl('/js/bower_components/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js'),
], 'footer');

$enqueue->front([
	'as'     => 'googleApi',
	'src'    => 'https://maps.googleapis.com/maps/api/js?key=AIzaSyCH2aMLtEaJUC81UK4M8H_Vb4ePt8iti9c&v=3.exp&libraries=places',
], 'footer');

$enqueue->front([
	'as'     => 'validator',
	'src'    => Helper::assetUrl('/js/validator.min.js'),
], 'footer');