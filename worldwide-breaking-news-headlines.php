<?php
/**
 * Plugin Name: 	Worldwide Breaking News Headlines
 * Plugin URI:		http://wordpress.org/plugins/worldwide-breaking-news-headlines/
 * Description: 	This Worldwide Breaking News Headlines plugin will display the world famous newspaper's top/recent news/headlines in your sidebar/widget area. Currently it showing news of CNN, BBC, The The Guardian (UK), Fox News and NBC News. In the future more to come.
 * Version: 		1.5
 * Author: 			Hossni Mubarak
 * Author URI: 		http://www.hossnimubarak.com
 * License:         GPL-2.0+
 * License URI:     http://www.gnu.org/licenses/gpl-2.0.txt
*/

if ( ! defined( 'WPINC' ) ) { die; }
if ( ! defined('ABSPATH') ) { exit; }

define( 'HMNFW_PATH', plugin_dir_path( __FILE__ ) );
define( 'HMNFW_ASSETS', plugins_url( '/assets/', __FILE__ ) );
define( 'HMNFW_SLUG', plugin_basename( __FILE__ ) );
define( 'HMNFW_PREFIX', 'hmnfw_' );
define( 'HMNFW_CLASSPREFIX', 'class-hm-newsfeed-' );
define( 'HMNFW_VERSION', '1.5' );

require_once HMNFW_PATH . 'inc/' . HMNFW_CLASSPREFIX . 'master.php';
new HMNFW_Master();
?>