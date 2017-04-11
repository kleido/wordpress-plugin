<?php

/**
 * @wordpress-plugin
 * Plugin Name:       WP Reservation-Engine
 * Plugin URI:        http://www.reservationengine.net/wp-plugin
 * Description:       A plugin to integrate the Reservation Engine's API into Wordpress
 * Version:           1.4.2
 * Author:            X!TE Communication Agency L.t.d
 * Author URI:        https://www.reservationengine.net
 * License:           MIT
 * Text Domain:		  reservation-engine
 * Domain Path:		  /languages/
 */

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/vendor/getherbert/framework/bootstrap/autoload.php';
herbert('Twig_Environment')->addExtension(new WPRengine\CustomTwigExtension());
// dd( dirname(plugin_basename( __FILE__ )) . '/languages/' );
load_plugin_textdomain( 'wordpress-rengine', false, dirname(plugin_basename( __FILE__ )) . '/languages/' );