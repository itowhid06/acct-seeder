<?php

/**
 * The core plugin class.
 */
class Acct_Seeder {

	protected $plugin_name;

	protected $version;

	protected $faker;

	public function __construct() {
		if ( defined( 'ACCT_SEEDER_VERSION' ) ) {
			$this->version = ACCT_SEEDER_VERSION;
		} else {
			$this->version = '1.0.0';
		}

		$this->plugin_name = 'acct-seeder';
		$this->faker = Faker\Factory::create();

		$this->seed_people();
	}

	private function seed_people() {
		global $wpdb;

		$people_table = $wpdb->prefix . 'erp_peoples';
		$people_type_relation_table = $wpdb->prefix . 'erp_people_type_relations';

		// truncate
		$wpdb->query("TRUNCATE TABLE {$people_table}");
		$wpdb->query("TRUNCATE TABLE {$people_type_relation_table}");

		// insert
		for ( $i = 0; $i < 50; $i++ ) {
			$wpdb->insert( 
				$people_table,
				[ 
					'first_name' => $this->faker->firstName,
					'last_name'  => $this->faker->lastName,
					'email'      => $this->faker->email
				],
				[
					'%s',
					'%s',
					'%s' 
				]
			);

			$wpdb->insert( 
				$people_type_relation_table,
				[ 
					'people_id'       => $i + 1,
					'people_types_id' => $this->faker->numberBetween(1, 4)
				],
				[
					'%d',
					'%d'
				]
			);
		}

	}

}
