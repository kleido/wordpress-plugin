<?php

/**
 * Plugin Name:       Workadu Reservation Engine
 * Plugin URI:        http://www.workadu.com/car-rental-software
 * Description:       A plugin to integrate the Workadu's API into Wordpress
 * Version:           2.3.4
 * Author:            X!TE Communication Agency L.t.d
 * Author URI:        https://www.workadu.com/car-rental-software
 * License:           MIT
 * Text Domain:		  wordpress-rengine
 * Domain Path:		  /languages/
 */

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/vendor/getherbert/framework/bootstrap/autoload.php';
require_once __DIR__ . '/vendor/stripe/init.php';
require_once __DIR__ . '/vendor/payzen/autoload.php';
herbert('Twig_Environment')->addExtension(new WPRengine\CustomTwigExtension());
// dd( dirname(plugin_basename( __FILE__ )) . '/languages/' );
load_plugin_textdomain( 'wordpress-rengine', false, dirname(plugin_basename( __FILE__ )) . '/languages/' );