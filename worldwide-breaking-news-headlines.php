<?php
/**
 * Plugin Name: 	Worldwide Breaking News Headlines
 * Plugin URI:		http://wordpress.org/plugins/worldwide-breaking-news-headlines/
 * Description: 	This Worldwide Breaking News Headlines plugin will display the world famous newspaper's top/recent news/headlines in your sidebar/widget area.
 * Version: 		1.6
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
define( 'HMNFW_CLASSPREFIX', 'cls-hmnfw-' );
define( 'HMNFW_TXT_DOMAIN', 'worldwide-breaking-news-headlines' );
define( 'HMNFW_VERSION', '1.6' );

require_once HMNFW_PATH . 'inc/' . HMNFW_CLASSPREFIX . 'master.php';
new HMNFW_Master();

add_action( 'wp_enqueue_scripts', 'hmnfw_front_assets' );
function hmnfw_front_assets() {
    wp_enqueue_style(
        'hmnfw-front-styles',
        HMNFW_ASSETS . 'hmnfw-front-styles.css',
        '',
        HMNFW_VERSION,
        FALSE
    );
}
add_action( 'admin_enqueue_scripts', 'hmnfw_admin_assets' );
function hmnfw_admin_assets() {
    
    if( is_admin() ) {

        wp_enqueue_style(
            'hmnfw-admin',
            HMNFW_ASSETS . 'hmnfw-admin-styles.css',
            '',
            HMNFW_VERSION,
            FALSE
        );

        if ( !wp_script_is('jquery') ) {
            wp_enqueue_script('jquery');
        }
        
        wp_enqueue_script(
            'hmnfw-admin-script',
            HMNFW_ASSETS . 'hmnfw-admin-script.js',
            array('jquery'),
            HMNFW_VERSION,
            TRUE
        );
    }
}
?>