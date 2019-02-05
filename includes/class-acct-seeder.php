<?php

/**
 * The core plugin class.
 */
class Acct_Seeder {

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
		$this->drop_db_tables();
		$this->toggle_erp_plugin();
		$this->call_dynamic_seeder();
	}

	/**
	 * Get All tables raw (without prefix) name
	 */
	private function get_tables_name() {
		$this->tables = [
			'erp_acct_bill_account_details',
			'erp_acct_bill_details',
			'erp_acct_bills',
			'erp_acct_cash_at_banks',
			'erp_acct_chart_of_accounts',
			'erp_acct_currency_info',
			'erp_acct_expense_checks',
			'erp_acct_expense_details',
			'erp_acct_expenses',
			'erp_acct_invoice_account_details',
			'erp_acct_invoice_details',
			'erp_acct_invoice_details_tax',
			'erp_acct_invoice_receipts',
			'erp_acct_invoice_receipts_details',
			'erp_acct_invoices',
			'erp_acct_journal_details',
			'erp_acct_journals',
			'erp_acct_ledger_categories',
			'erp_acct_ledger_details',
			'erp_acct_ledger_settings',
			'erp_acct_ledgers',
			'erp_acct_opening_balance',
			'erp_acct_pay_bill',
			'erp_acct_pay_bill_details',
			'erp_acct_pay_purchase',
			'erp_acct_pay_purchase_details',
			'erp_acct_payment_methods',
			'erp_acct_people_details',
			'erp_acct_people_trn',
			'erp_acct_product_categories',
			'erp_acct_product_details',
			'erp_acct_product_types',
			'erp_acct_products',
			'erp_acct_purchase',
			'erp_acct_purchase_account_details',
			'erp_acct_purchase_details',
			'erp_acct_tax_agencies',
			'erp_acct_tax_categories',
			'erp_acct_tax_items',
			'erp_acct_tax_pay',
			'erp_acct_tax_sales_tax_categories',
			'erp_acct_taxes',
			'erp_acct_transfer_voucher',
			'erp_acct_trn_status_types',
			'erp_acct_voucher_no',
			'erp_company_locations',
			'erp_people_type_relations',
			'erp_people_types',
			'erp_peoplemeta',
			'erp_peoples'
		];
	}

	/**
	 * Toggle erp plugin state
	 */
	private function toggle_erp_plugin() {
	    if ( ! function_exists('activate_plugin') ) {
	        require_once ABSPATH . 'wp-admin/includes/plugin.php';
	    }

	    $wp_erp = 'wp-erp/wp-erp.php';

	    if ( is_plugin_active( $wp_erp ) ) {
	    	deactivate_plugins( $wp_erp );
	    }

	    activate_plugin( $wp_erp );
	}

	/**
	 * DROP tables data before insert
	 */
	private function drop_db_tables() {
		global $wpdb;

		foreach ($this->tables as $table) {
			$wpdb->query('DROP TABLE IF EXISTS ' . $wpdb->prefix . $table);
		}
	}

	/**
	 * Call seeder methods dynamically
	 *
	 * e.g. seed_erp_company_locations()
	 */
	private function call_dynamic_seeder() {
		foreach ($this->tables as $table) {
			$method_name = 'seed_' . $table;

			if ( method_exists($this, $method_name) ) {
				$this->{'seed_' . $table}($table);
			}
		}
	}

	/**
	 * Utility method
	 * slugify
	 */
	private function slugify($str) {
		// replace non letter or digits by _
		$str = preg_replace('~[^\pL\d]+~u', '_', $str);

		return strtolower($str);
	}

	/**
	 * Utility method
	 * chart_id
	 */
	private function get_chart_id_by_slug($key) {
		switch ($key) {
			case 'asset':
				$id = 1;
				break;
			case 'liability':
				$id = 2;
				break;
			case 'equity':
				$id = 3;
				break;
			case 'income':
				$id = 4;
				break;
			case 'expense':
				$id = 5;
				break;
			default:
				$id = null;
		}

		return $id;
	}

	/* ======================================================
	|
	| Methods for seed
	|
	| =================================== */

	/**
	 * erp_acct_chart_of_accounts
	 */
	private function seed_erp_acct_chart_of_accounts( $table_name ) {
		global $wpdb;
		$table = $wpdb->prefix . $table_name;

		$charts = ['Asset', 'Liability', 'Equity', 'Income', 'Expense', 'Asset & Liability', 'Bank'];

		for ( $i = 0; $i < count($charts); $i++ ) {
			$wpdb->insert( $table, [
				'name' => $charts[$i], 
				'slug' => $this->slugify($charts[$i]) 
			] );
		}
	}

	/**
	 * erp_acct_currency_info
	 */
	private function seed_erp_acct_currency_info( $table_name ) {
		global $wpdb;
		$table = $wpdb->prefix . $table_name;

		$currencies = [
			['name' => 'Dollar', 'sign' => '$'],
			['name' => 'Euro', 'sign' => '€']
		];

		for ( $i = 0; $i < count($currencies); $i++ ) {
			$wpdb->insert( $table, [
				'name' => $currencies[$i]['name'],
				'sign' => $currencies[$i]['sign']
			] );
		}
	}

	/**
	 * erp_acct_journals
	 */
	private function seed_erp_acct_journals( $table_name ) {
		global $wpdb;
		$table = $wpdb->prefix . $table_name;

		$wpdb->insert( $table, [
			'trn_date'       => date('Y-m-d'),
			'voucher_no'     => 1, // journal -> voucher_no
			'voucher_amount' => 10000.00,
			'particulars'    => 'Initial Journal Entry'
		] );
	}

	/**
	 * erp_acct_journal_details
	 */
	private function seed_erp_acct_journal_details( $table_name ) {
		global $wpdb;
		$table = $wpdb->prefix . $table_name;

		$journal_details = [
			[
				'ledger_id'   => 1,
				'particulars' => 'Cash In Hand',
				'debit'       => 5000.00,
				'credit'      => 0.00
			],
			[
				'ledger_id'   => 2,
				'particulars' => 'Cash At Bank',
				'debit'       => 5000.00,
				'credit'      => 0.00
			],
			[
				'ledger_id'   => 45,
				'particulars' => 'Revenue From Sale',
				'debit'       => 0.00,
				'credit'      => 10000.00
			],
		];

		foreach ( $journal_details as $journal_detail ) {
			$wpdb->insert( $table, [
				'trn_no'      => 1,
				'ledger_id'   => $journal_detail['ledger_id'],
				'particulars' => $journal_detail['particulars'],
				'debit'       => $journal_detail['debit'],
				'credit'      => $journal_detail['credit']
			] );
		}
	}

	/**
	 * erp_acct_ledger_details
	 */
	private function seed_erp_acct_ledger_details( $table_name ) {
		global $wpdb;
		$table = $wpdb->prefix . $table_name;


		$ledger_details = [
			[
				'ledger_id'   => 1,
				'particulars' => 'Cash In Hand',
				'debit'       => 5000.00,
				'credit'      => 0.00
			],
			[
				'ledger_id'   => 2,
				'particulars' => 'Cash At Bank',
				'debit'       => 5000.00,
				'credit'      => 0.00
			],
			[
				'ledger_id'   => 45,
				'particulars' => 'Revenue From Sale',
				'debit'       => 0.00,
				'credit'      => 10000.00
			],
		];

		foreach ( $ledger_details as $ledger_detail ) {
			$wpdb->insert( $table, [
				'trn_no'      => 1,
				'ledger_id'   => $ledger_detail['ledger_id'],
				'particulars' => $ledger_detail['particulars'],
				'debit'       => $ledger_detail['debit'],
				'credit'      => $ledger_detail['credit'],
				'trn_date'    => date('Y-m-d')
			] );
		}
	}

	/**
	 * erp_acct_ledgers
	 */
	private function seed_erp_acct_ledgers( $table_name ) {
		global $wpdb;
		$table = $wpdb->prefix . $table_name;

		$ledgers = [
			'asset' => [
				['name' => 'Cash', 'system' => 1],
				['name' => 'Bank Balance', 'system' => 1],
				['name' => 'Accounts Receivable', 'system' => null],
				['name' => 'Allowance for Doubtful Accounts', 'system' => null],
				['name' => 'Interest Receivable', 'system' => null],
				['name' => 'Inventory', 'system' => null],
				['name' => 'Supplies', 'system' => null],
				['name' => 'Prepaid Insurance', 'system' => null],
				['name' => 'Prepaid Rent', 'system' => null],
				['name' => 'Prepaid Salary', 'system' => null],
				['name' => 'Land', 'system' => null],
				['name' => 'Equipment', 'system' => null],
				['name' => 'Furniture & Fixture', 'system' => null],
				['name' => 'Buildings', 'system' => null],
				['name' => 'Copyrights', 'system' => null],
				['name' => 'Goodwill', 'system' => null],
				['name' => 'Patents', 'system' => null],
				['name' => 'Accoumulated Depreciation- Equipment', 'system' => null],
				['name' => 'Accoumulated Depreciation- Buildings', 'system' => null],
				['name' => 'Accoumulated Depreciation- Furniture & Fixtur', 'system' => null]
			],
			
			'liability' => [
				['name' => 'Notes Payable', 'system' => null],
				['name' => 'Accounts Payable', 'system' => null],
				['name' => 'Unearned Revenue', 'system' => null],
				['name' => 'Salaries and Wages Payable', 'system' => null],
				['name' => 'Unearned Rent Revenue', 'system' => null],
				['name' => 'Interest Payable', 'system' => null],
				['name' => 'Dividends Payable', 'system' => null],
				['name' => 'Income Tax Payable', 'system' => null],
				['name' => 'Sales Tax Payable', 'system' => null],
				['name' => 'Bonds Payable', 'system' => null],
				['name' => 'Discount on Bonds Payable', 'system' => null],
				['name' => 'Pfemium on Bonds Payable', 'system' => null],
				['name' => 'Mortgage Payable', 'system' => null]
			],

			'equity' => [
				['name' => 'Owner\'s Capital', 'system' => null],
				['name' => 'Owner\'s  Drawings', 'system' => null],
				['name' => 'Common Stock', 'system' => null],
				['name' => 'Paid- in Capital in Excess of Par- Common Stock', 'system' => null],
				['name' => 'Paid- in Capital in Excess of Par- Preferred Stock', 'system' => null],
				['name' => 'Preferred Stock', 'system' => null],
				['name' => 'Treasury Stock', 'system' => null],
				['name' => 'Retained Earnings', 'system' => null],
				['name' => 'Dividends', 'system' => null],
				['name' => 'Income Summary', 'system' => null]
			],

			'income' => [
				['name' => 'Service Revenue', 'system' => null],
				['name' => 'Sales Revenue', 'system' => null],
				['name' => 'Sales Discounts', 'system' => null],
				['name' => 'Sales Returns and Allowance', 'system' => null],
				['name' => 'Interest Revenue', 'system' => null],
				['name' => 'Gain on Disposal of Plant Assets', 'system' => null]
			],

			'expense' => [
				['name' => 'Advertising Expense', 'system' => null],
				['name' => 'Amortization Expense', 'system' => null],
				['name' => 'Bad Debt Expense', 'system' => null],
				['name' => 'Cost of Goods Sold', 'system' => null],
				['name' => 'Depreciation Expense', 'system' => null],
				['name' => 'Freight -Out', 'system' => null],
				['name' => 'Income Tax Expense', 'system' => null],
				['name' => 'Insurance Expense', 'system' => null],
				['name' => 'Interest Expense', 'system' => null],
				['name' => 'Loss on Disposal of Plant Assets', 'system' => null],
				['name' => 'Maintenance and Repairs Expense', 'system' => null],
				['name' => 'Salaries and  wages Expense', 'system' => null],
				['name' => 'Rent Expense', 'system' => null],
				['name' => 'Supplies Expense', 'system' => null],
				['name' => 'Utilites Expense', 'system' => null]
			]
		];

		foreach ( array_keys( $ledgers ) as $array_key ) {
			foreach ( $ledgers[$array_key] as $value ) {
				$wpdb->insert( 
					$table,
					[ 
						'chart_id' => $this->get_chart_id_by_slug($array_key),
						'name'     => $value['name'],
						'slug'     => $this->slugify($value['name']),
						'system'   => $this->slugify($value['system'])
					]
				);
			}
		}
	}

	/**
	 * erp_people_type_relations
	 */
	private function seed_erp_people_type_relations( $table_name ) {
		global $wpdb;
		$table = $wpdb->prefix . $table_name;

		for ( $i = 0; $i < $this->limit; $i++ ) {
			$wpdb->insert( 
				$table,
				[ 
					'people_id'       => $i + 1,
					'people_types_id' => $this->faker->numberBetween(1, 5)
				]
			);
		}
	}

	/**
	 * erp_acct_trn_status_types
	 */
	private function seed_erp_acct_trn_status_types( $table_name ) {
		global $wpdb;
		$table = $wpdb->prefix . $table_name;

		$statuses = [
			'Draft',
			'Awaiting Approval',
			'Pending',
			'Paid',
			'Partially_paid',
			'Approved',
			'Bounced',
			'Closed',
			'Void'
		];

		for ( $i = 0; $i < count($statuses); $i++ ) {
			$wpdb->insert( $table, [
				'type_name' => $statuses[$i],
				'slug'      => $this->slugify( $statuses[$i] )
			] );
		}
	}

	/**
	 * erp_acct_product_categories
	 */
	private function seed_erp_acct_product_categories( $table_name ) {
		global $wpdb;
		$table = $wpdb->prefix . $table_name;

		$categories = ['Mobile', 'Lifecare'];

		for ( $i = 0; $i < count($categories); $i++ ) {
			$wpdb->insert( $table, [ 'name' => $categories[$i] ] );
		}
	}

	/**
	 * erp_acct_product_types
	 */
	private function seed_erp_acct_product_types( $table_name ) {
		global $wpdb;
		$table = $wpdb->prefix . $table_name;

		$types = ['Product', 'Service'];

		for ( $i = 0; $i < count($types); $i++ ) {
			$wpdb->insert( $table, [ 'name' => $types[$i] ] );
		}
	}

	/**
	 * erp_acct_products
	 */
	private function seed_erp_acct_products( $table_name ) {
		global $wpdb;
		$table = $wpdb->prefix . $table_name;

		$products = [
			'iPhone',
			'Samsung',
			'Huawei',
			'Oneplus',
			'Nokia',
			'Motorola',
			'Lenovo',
			'Asus',
			'Lava'
		];

		$services = [
			'Graffiti Abatement',
			'Dry-Cleaning',
			'Mobile Locksmith',
			'Diaper Delivery',
			'Golf-Club Cleaning',
			'Self-Defense Instructor',
			'Pet Sitting',
			'Court-Paper Serving',
			'Personal Chef'
		];

		for ( $i = 0; $i < count($products); $i++ ) {
			$cost_price = $this->faker->randomFloat(2, 8, 88);
			$sale_price = $cost_price + $this->faker->numberBetween(3, 9);

			$wpdb->insert( $table, [
				'name'            => $products[$i],
				'product_type_id' => 1,
				'category_id'     => 1,
				'cost_price'      => $cost_price,
				'sale_price'      => $sale_price
			] );
		}

		for ( $i = 0; $i < count($services); $i++ ) {
			$cost_price = $this->faker->randomFloat(2, 9, 99);
			$sale_price = $cost_price + $this->faker->numberBetween(4, 6);

			$wpdb->insert( $table, [
				'name'            => $services[$i],
				'product_type_id' => 2,
				'category_id'     => 2,
				'cost_price'      => $cost_price,
				'sale_price'      => $sale_price
			] );
		}
	}

	/**
	 * erp_acct_voucher_no
	 */
	private function seed_erp_acct_voucher_no( $table_name ) {
		global $wpdb;
		$table = $wpdb->prefix . $table_name;

		$wpdb->insert( $table, [ 'type' => 'journal' ] );
	}

	/**
	 * erp_peoplemeta
	 */
	private function seed_erp_people_types( $table_name ) {
		global $wpdb;
		$table = $wpdb->prefix . $table_name;

		$types = ['contact', 'company', 'customer', 'vendor', 'employee'];

		for ( $i = 0; $i < count($types); $i++ ) {
			$wpdb->insert( $table, [ 'name' => $types[$i] ] );
		}
	}

	/**
	 * erp_peoples
	 */
	private function seed_erp_peoples( $table_name ) {
		global $wpdb;
		$table = $wpdb->prefix . $table_name;

		for ( $i = 0; $i < $this->limit; $i++ ) {
			$wpdb->insert( 
				$table,
				[ 
					'first_name'  => $this->faker->firstName,
					'last_name'   => $this->faker->lastName,
					'email'       => $this->faker->email,
					'phone'       => $this->faker->e164PhoneNumber,
					'street_1'    => $this->faker->streetName,
					'street_2'    => $this->faker->streetAddress,
					'city'        => $this->faker->city,
					'state'       => $this->faker->state,
					'postal_code' => $this->faker->postcode,
					'country'     => $this->faker->country
				]
			);
		}
	}

}
