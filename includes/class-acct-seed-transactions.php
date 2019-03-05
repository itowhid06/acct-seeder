<?php

/**
 * The transactions seeder plugin class.
 */
class Acct_Transactions_Seeder {

	protected $plugin_name;

	protected $version;

    private $faker;

    private $tables;

	private $limit;

	public function __construct() {
		if ( defined( 'ACCT_SEEDER_VERSION' ) ) {
			$this->version = ACCT_SEEDER_VERSION;
		} else {
			$this->version = '1.0.0';
        }

		$this->plugin_name = 'acct-seeder';
        $this->faker = Faker\Factory::create();
		$this->limit = 30;

		$this->get_tables_name();
		$this->call_dynamic_seeder();
    }

	/**
	 * Get All tables raw (without prefix) name
	 */
	private function get_tables_name() {
		$this->tables = [
			'erp_acct_invoice_account_details',
			'erp_acct_invoice_details',
			'erp_acct_invoice_details_tax',
			'erp_acct_invoice_receipts',
			'erp_acct_invoice_receipts_details',
			'erp_acct_invoices',
			'erp_acct_ledger_details'
		];
	}

	/**
	 * Call seeder methods
	 */
	private function call_dynamic_seeder() {
		foreach ($this->tables as $table) {
			$method_name = 'seed_' . $table;

			if ( method_exists($this, $method_name) ) {
				$this->{'seed_' . $table}($table);
			}
		}
    }

	/* ======================================================
	|
	| Methods for seed
	|
	| =================================== */

	/**
	 * erp_acct_chart_of_accounts
	 */
	private function seed_erp_acct_invoices( $table_name ) {
		global $wpdb;
        $table = $wpdb->prefix . $table_name;

        // $people = erp_get_peoples( [ 'type' => 'customer' ] );
        // $people_length = count( $people );


		// for ( $i = 2; $i < $this->limit; $i++ ) {
		// 	$rand_index = $this->faker->numberBetween( 1, $people_length - 1 );
		// 	$amount = $this->faker->numberBetween(99, 999);

		// 	$wpdb->insert( $table, [
		// 		'voucher_no'      => $i,
		// 		'customer_id'     => $people[$rand_index]['id'],
		// 		'customer_name'   => $people[$rand_index]['first_name'] . ' ' . $people[$rand_index]['last_name'],
		// 		'trn_date'        => $this->faker->date(),
		// 		'due_date'        => $this->faker->date(),
		// 		'billing_address' => $this->faker->country . ' ' . $this->faker->address,
		// 		'amount'          => $amount,
		// 		'discount'        => 20,
		// 		'discount_type'   => 'discount-percent',
		// 		'tax_rate_id'     => 1,
		// 		'tax'             => 40,
		// 		'estimate'        => 0,
		// 		'attachments'     => [],
		// 		'status'          => 2,
		// 		'particulars'     => '',
		// 		'created_at'      => $this->faker->date()
		// 	] );
		// }

	}

}
