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
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'ACCT_SEEDER_VERSION', '1.0.0' );

require_once plugin_dir_path( __FILE__ ) . 'vendor/fzaninotto/faker/src/autoload.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-acct-seeder.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-acct-seeder-activator.php';

function activate_acct_seeder() {
	Acct_Seeder_Activator::activate();
}

register_activation_hook( __FILE__, 'activate_acct_seeder' );


/**
 * Add accounting seeder CLI feature
 */
if ( defined( 'WP_CLI' ) && WP_CLI ) {
    class AcctSeederCLI {

        public function run_seeder() {
			WP_CLI::error('Unavailable !');
			// WP_CLI::runcommand('plugin deactivate acct-seeder');
			// WP_CLI::runcommand('plugin activate acct-seeder');

            // WP_CLI::success( 'Done!' );
        }

	}

	WP_CLI::add_command( 'acct', 'AcctSeederCLI' );
}
