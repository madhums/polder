<?php
/**
 * Plugin Name: Team Section - Block
 * Description: Makes background element scrolls slower than foreground content.
 * Version: 1.0.8
 * Author: bPlugins LLC
 * Author URI: http://bplugins.com
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain: team-section
 */

// ABS PATH
if ( !defined( 'ABSPATH' ) ) { exit; }

// Constant
define( 'TSB_VERSION', isset( $_SERVER['HTTP_HOST'] ) && 'localhost' === $_SERVER['HTTP_HOST'] ? time() : '1.0.8' );
define( 'TSB_DIR_URL', plugin_dir_url( __FILE__ ) );
define( 'TSB_DIR_PATH', plugin_dir_path( __FILE__ ) );

require_once TSB_DIR_PATH . 'inc/block.php';