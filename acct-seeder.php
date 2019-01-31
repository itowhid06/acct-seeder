<?php

/**
 * @wordpress-plugin
 * Plugin Name:       Accounting Seeder
 * Plugin URI:        https://wperp.com
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Nadim
 * Author URI:        https://github.com/nadim1992
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       acct-seeder
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'ACCT_SEEDER_VERSION', '1.0.0' );

require_once plugin_dir_path( __FILE__ ) . 'vendor/fzaninotto/faker/src/autoload.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-acct-seeder.php';

function activate_acct_seeder() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-acct-seeder-activator.php';
	Acct_Seeder_Activator::activate();
}

function deactivate_acct_seeder() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-acct-seeder-deactivator.php';
	Acct_Seeder_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_acct_seeder' );
register_deactivation_hook( __FILE__, 'deactivate_acct_seeder' );

