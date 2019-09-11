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

		// Update wp_options
		update_option( 'erp_tracking_notice', 'hide' );
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
			'erp_acct_financial_years',
			'erp_acct_opening_balances',
			'erp_acct_pay_bill',
			'erp_acct_pay_bill_details',
			'erp_acct_pay_purchase',
			'erp_acct_pay_purchase_details',
			'erp_acct_payment_methods',
			'erp_acct_people_account_details',
			'erp_acct_people_trn',
			'erp_acct_people_trn_details',
			'erp_acct_product_categories',
			'erp_acct_product_details',
			'erp_acct_product_types',
			'erp_acct_products',
			'erp_acct_purchase',
			'erp_acct_purchase_account_details',
			'erp_acct_purchase_details',
			'erp_acct_tax_agencies',
			'erp_acct_tax_cat_agency',
			'erp_acct_tax_agency_details',
			'erp_acct_tax_categories',
			'erp_acct_tax_pay',
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
			case 'asset_liability':
				$id = 6;
				break;
			case 'bank':
				$id = 7;
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
	| Note: If seeder method starts with _ (underscore), they won't call dynamically
	|
	| =================================== */

	/**
	 * erp_acct_chart_of_accounts
	 */
	private function _seed_erp_acct_chart_of_accounts( $table_name ) {
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
	private function _seed_erp_acct_currency_info( $table_name ) {
		global $wpdb;
		$table = $wpdb->prefix . $table_name;

		$currencies = [
			['name' => 'USD', 'sign' => '$'],
			['name' => 'EUR', 'sign' => '€']
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
				'ledger_id'   => 7,
				'particulars' => 'Cash In Hand',
				'debit'       => 10000.00,
				'credit'      => 0.00,
				'created_at'  => date('Y-m-d')
			],
			[
				'ledger_id'   => 94,
				'particulars' => 'Revenue From Sale',
				'debit'       => 0.00,
				'credit'      => 10000.00,
				'created_at'  => date('Y-m-d')
			],
		];

		foreach ( $journal_details as $journal_detail ) {
			$wpdb->insert( $table, [
				'trn_no'      => 1,
				'ledger_id'   => $journal_detail['ledger_id'],
				'particulars' => $journal_detail['particulars'],
				'debit'       => $journal_detail['debit'],
				'credit'      => $journal_detail['credit'],
				'created_at'  => $journal_detail['created_at']
			] );
		}
	}

	/**
	 * erp_acct_ledger_categories
	 */
	private function seed_erp_acct_ledger_categories( $table_name ) {
		global $wpdb;
		$table = $wpdb->prefix . $table_name;

		$categories = [
			[
				'name'      => 'Furniture',
				'chart_id'  => 1,
				'parent_id' => null,
				'system'    => null
			],
			[
				'name'      => 'Chair',
				'chart_id'  => 1,
				'parent_id' => 1,
				'system'    => null
			],
			[
				'name'      => 'Table',
				'chart_id'  => 1,
				'parent_id' => 1,
				'system'    => null
			],
			[
				'name'      => 'Device',
				'chart_id'  => 1,
				'parent_id' => 0,
				'system'    => null
			],
			[
				'name'      => 'MacBook',
				'chart_id'  => 1,
				'parent_id' => 4,
				'system'    => null
			],
			[
				'name'      => 'Direct Expense',
				'chart_id'  => 5,
				'parent_id' => null,
				'system'    => 1
			],
			[
				'name'      => 'Indirect Expense',
				'chart_id'  => 5,
				'parent_id' => null,
				'system'    => 1
			],
		];

		foreach ( $categories as $category ) {
			$wpdb->insert( $table, [
				'name'      => $category['name'],
				'slug'      => $this->slugify( $category['name'] ),
				'chart_id'  => $category['chart_id'],
				'parent_id' => $category['parent_id'],
				'system'    => $category['system']
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
				'ledger_id'   => 7,
				'particulars' => 'Cash In Hand',
				'debit'       => 10000.00,
				'credit'      => 0.00
			],
			[
				'ledger_id'   => 86,
				'particulars' => 'Owner\'s Capital',
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
	private function _seed_erp_acct_ledgers( $table_name ) {
		global $wpdb;
		$table = $wpdb->prefix . $table_name;

		$ledgers = [
			'asset' => [
				['name' => 'Cash', 'system' => 1],
				['name' => 'Allowance for Doubtful Accounts', 'system' => 1],
				['name' => 'Interest Receivable', 'system' => 1],
				['name' => 'Inventory', 'system' => 1],
				['name' => 'Supplies', 'system' => 1],
				['name' => 'Prepaid Insurance', 'system' => 1],
				['name' => 'Prepaid Rent', 'system' => 1],
				['name' => 'Prepaid Salary', 'system' => 1],
				['name' => 'Land', 'system' => null],
				['name' => 'Equipment', 'system' => null],
				['name' => 'Furniture & Fixture', 'system' => 1],
				['name' => 'Buildings', 'system' => null],
				['name' => 'Copyrights', 'system' => 1],
				['name' => 'Goodwill', 'system' => 1],
				['name' => 'Patents', 'system' => 1],
				['name' => 'Accoumulated Depreciation- Equipment', 'system' => null],
				['name' => 'Accoumulated Depreciation- Buildings', 'system' => 1],
				['name' => 'Accoumulated Depreciation- Furniture & Fixtur', 'system' => 1]
			],

			'liability' => [
				['name' => 'Notes Payable', 'system' => 1],
				['name' => 'Unearned Revenue', 'system' => null],
				['name' => 'Salaries and Wages Payable', 'system' => 1],
				['name' => 'Unearned Rent Revenue', 'system' => 1],
				['name' => 'Interest Payable', 'system' => null],
				['name' => 'Dividends Payable', 'system' => 1],
				['name' => 'Income Tax Payable', 'system' => null],
				['name' => 'Bonds Payable', 'system' => 1],
				['name' => 'Discount on Bonds Payable', 'system' => null],
				['name' => 'Pfemium on Bonds Payable', 'system' => 1],
				['name' => 'Mortgage Payable', 'system' => 1]
			],

			'equity' => [
				['name' => 'Owner\'s Equity', 'system' => 1],
				['name' => 'Common Stock', 'system' => 1],
				['name' => 'Paid- in Capital in Excess of Par- Common Stock', 'system' => null],
				['name' => 'Paid- in Capital in Excess of Par- Preferred Stock', 'system' => 1],
				['name' => 'Preferred Stock', 'system' => 1],
				['name' => 'Treasury Stock', 'system' => 1],
				['name' => 'Retained Earnings', 'system' => 1],
				['name' => 'Dividends', 'system' => 1],
				['name' => 'Income Summary', 'system' => 1]
			],

			'income' => [
				['name' => 'Service Revenue', 'system' => 1],
				['name' => 'Sales Revenue', 'system' => 1],
				['name' => 'Sales Discounts', 'system' => 1],
				['name' => 'Sales Returns and Allowance', 'system' => 1],
				['name' => 'Interest Revenue', 'system' => 1],
				['name' => 'Gain on Disposal of Plant Assets', 'system' => 1]
			],

			'expense' => [
				['name' => 'Advertising Expense', 'system' => 1],
				['name' => 'Amortization Expense', 'system' => 1],
				['name' => 'Bad Debt Expense', 'system' => null],
				['name' => 'Cost of Goods Sold', 'system' => 1],
				['name' => 'Depreciation Expense', 'system' => 1],
				['name' => 'Freight -Out', 'system' => 1],
				['name' => 'Income Tax Expense', 'system' => null],
				['name' => 'Insurance Expense', 'system' => 1],
				['name' => 'Interest Expense', 'system' => 1],
				['name' => 'Loss on Disposal of Plant Assets', 'system' => 1],
				['name' => 'Maintenance and Repairs Expense', 'system' => 1],
				['name' => 'Salaries and wages Expense', 'system' => 1],
				['name' => 'Rent Expense', 'system' => 1],
				['name' => 'Supplies Expense', 'system' => 1],
				['name' => 'Utilites Expense', 'system' => 1]
			],

			// 'asset_liability' => [],

			'bank' => [
				['name' => 'Standard Chartered Bank', 'system' => null],
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
						'system'   => $value['system']
					]
				);
			}
		}
	}

	/**
	 * erp_acct_financial_years
	 */
	private function _seed_erp_acct_financial_years( $table_name ) {
		global $wpdb;
		$table = $wpdb->prefix . $table_name;

		$f_years = [
			[
				'name'        => 'FY: 2018',
				'start_date'  => '2018-01-01',
				'end_date'    => '2018-12-31',
				'description' => 'Financial Year 2018',
				'created_at'  => date('Y-m-d'),
				'created_by'  => 1
			],
			[
				'name'        => 'FY: 2019',
				'start_date'  => '2019-01-01',
				'end_date'    => '2019-12-31',
				'description' => 'Financial Year 2019',
				'created_at'  => date('Y-m-d'),
				'created_by'  => 1
			]
		];

		foreach ( $f_years as $f_year ) {
			$wpdb->insert(
				$table,
				[
					'name'        => $f_year['name'],
					'start_date'  => $f_year['start_date'],
					'end_date'    => $f_year['end_date'],
					'description' => $f_year['description'],
					'created_at'  => date('Y-m-d'),
					'created_by'  => 1
				]
			);
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
	private function _seed_erp_acct_trn_status_types( $table_name ) {
		global $wpdb;
		$table = $wpdb->prefix . $table_name;

		$statuses = [
			'Draft',
			'Awaiting Payment',
			'Pending',
			'Paid',
			'Partially Paid',
			'Approved',
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
	 * erp_acct_payment_methods
	 */
	private function _seed_erp_acct_payment_methods( $table_name ) {
		global $wpdb;
		$table = $wpdb->prefix . $table_name;

		$methods = ['Cash', 'Bank', 'Check'];

		for ( $i = 0; $i < count($methods); $i++ ) {
			$wpdb->insert( $table, [ 'name' => $methods[$i] ] );
		}
	}

	/**
	 * erp_acct_product_categories
	 */
	private function seed_erp_acct_product_categories( $table_name ) {
		global $wpdb;
		$table = $wpdb->prefix . $table_name;

		$categories = ['Beverage', 'Plugin', 'Fast food', 'Chemical solvent'];

		for ( $i = 0; $i < count($categories); $i++ ) {
			$wpdb->insert( $table, [ 'name' => $categories[$i] ] );
		}
	}

	/**
	 * erp_acct_product_types
	 */
	private function _seed_erp_acct_product_types( $table_name ) {
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
			[
				'name'        => '7up',
				'category_id' => 1,
				'tax_cat_id'  => 3,
				'cost_price'  => 40,
				'sale_price'  => 50
			],
			[
				'name'        => 'weDevs Dokan',
				'category_id' => 2,
				'tax_cat_id'  => 1,
				'cost_price'  => 90,
				'sale_price'  => 100
			],
			[
				'name'        => 'Burger',
				'category_id' => 3,
				'tax_cat_id'  => 2,
				'cost_price'  => 120,
				'sale_price'  => 150
			]
		];

		$services = [
			[
				'name'        => 'Dry cleaning',
				'category_id' => 4,
				'tax_cat_id'  => null,
				'cost_price'  => 200,
				'sale_price'  => 250
			]
		];

		for ( $i = 0; $i < count($products); $i++ ) {
			$wpdb->insert( $table, [
				'name'            => $products[$i]['name'],
				'product_type_id' => 1,
				'category_id'     => $products[$i]['category_id'],
				'tax_cat_id'      => $products[$i]['tax_cat_id'],
				'cost_price'      => $products[$i]['cost_price'],
				'sale_price'      => $products[$i]['sale_price']
			] );
		}

		for ( $i = 0; $i < count($services); $i++ ) {
			$wpdb->insert( $table, [
				'name'            => $services[$i]['name'],
				'product_type_id' => 2,
				'category_id'     => $services[$i]['category_id'],
				'tax_cat_id'      => $services[$i]['tax_cat_id'],
				'cost_price'      => $services[$i]['cost_price'],
				'sale_price'      => $services[$i]['sale_price']
			] );
		}
	}

	/**
	 * erp_acct_tax_agencies
	 */
	private function seed_erp_acct_tax_agencies( $table_name ) {
		global $wpdb;
		$table = $wpdb->prefix . $table_name;

		$agencies = ['Arizona Department of Revenue', 'State Board of Equalization'];

		for ( $i = 0; $i < count($agencies); $i++ ) {
			$wpdb->insert( $table, [ 'name' => $agencies[$i] ] );
		}
	}

	/**
	 * erp_acct_tax_cat_agency
	 */
	private function seed_erp_acct_tax_cat_agency( $table_name ) {
		global $wpdb;
		$table = $wpdb->prefix . $table_name;

		$cat_agencies = [
			[
				'tax_id'         => 1,
				'component_name' => 'Software Tax',
				'tax_cat_id'     => 1,
				'agency_id'      => 1,
				'tax_rate'       => 5
			],
			[
				'tax_id'         => 1,
				'component_name' => 'Food Tax',
				'tax_cat_id'     => 2,
				'agency_id'      => 1,
				'tax_rate'       => 4
			],
			[
				'tax_id'         => 1,
				'component_name' => 'Food Tax',
				'tax_cat_id'     => 2,
				'agency_id'      => 2,
				'tax_rate'       => 4
			],
			[
				'tax_id'         => 1,
				'component_name' => 'Drink Tax',
				'tax_cat_id'     => 3,
				'agency_id'      => 1,
				'tax_rate'       => 8
			],
			[
				'tax_id'         => 1,
				'component_name' => 'Housing Tax',
				'tax_cat_id'     => 4,
				'agency_id'      => 1,
				'tax_rate'       => 10
			],
			[
				'tax_id'         => 2,
				'component_name' => 'Software Tax',
				'tax_cat_id'     => 1,
				'agency_id'      => 1,
				'tax_rate'       => 7
			],
			[
				'tax_id'         => 2,
				'component_name' => 'Food Tax',
				'tax_cat_id'     => 2,
				'agency_id'      => 1,
				'tax_rate'       => 12
			],
			[
				'tax_id'         => 2,
				'component_name' => 'Drink Tax',
				'tax_cat_id'     => 3,
				'agency_id'      => 1,
				'tax_rate'       => 7
			],
			[
				'tax_id'         => 2,
				'component_name' => 'Drink Tax',
				'tax_cat_id'     => 3,
				'agency_id'      => 2,
				'tax_rate'       => 12
			],
			[
				'tax_id'         => 2,
				'component_name' => 'Housing Tax',
				'tax_cat_id'     => 4,
				'agency_id'      => 1,
				'tax_rate'       => 2
			],
			[
				'tax_id'         => 3,
				'component_name' => 'Software Tax',
				'tax_cat_id'     => 1,
				'agency_id'      => 1,
				'tax_rate'       => 3
			],
			[
				'tax_id'         => 3,
				'component_name' => 'Drink Tax',
				'tax_cat_id'     => 3,
				'agency_id'      => 1,
				'tax_rate'       => 17
			]
		];

		for ( $i = 0; $i < count($cat_agencies); $i++ ) {
			$wpdb->insert( $table, [
				'tax_id'         => $cat_agencies[$i]['tax_id'],
				'component_name' => $cat_agencies[$i]['component_name'],
				'tax_cat_id'     => $cat_agencies[$i]['tax_cat_id'],
				'agency_id'      => $cat_agencies[$i]['agency_id'],
				'tax_rate'       => $cat_agencies[$i]['tax_rate']
			] );
		}
	}
	/**
	 * erp_acct_tax_categories
	 */
	private function seed_erp_acct_tax_categories( $table_name ) {
		global $wpdb;
		$table = $wpdb->prefix . $table_name;

		$tax_categories = [
			[
				'name'        => 'Software Service',
				'description' => 'Software development'
			],
			[
				'name'        => 'Food',
				'description' => 'Eat healthy'
			],
			[
				'name'        => 'Soft Drinks',
				'description' => 'Beverage'
			],
			[
				'name'        => 'Home',
				'description' => 'Live long'
			]
		];

		for ( $i = 0; $i < count($tax_categories); $i++ ) {
			$wpdb->insert( $table, [
				'name'        => $tax_categories[$i]['name'],
				'description' => $tax_categories[$i]['description']
			] );
		}
	}

	/**
	 * erp_acct_taxes
	 */
	private function seed_erp_acct_taxes( $table_name ) {
		global $wpdb;
		$table = $wpdb->prefix . $table_name;

		$taxes = [
			[
				'tax_rate_name' => 'California',
				'tax_number'    => 100011,
				'default'       => 1
			],
			[
				'tax_rate_name' => 'Tucson',
				'tax_number'    => 100012,
				'default'       => 0
			],
			[
				'tax_rate_name' => 'Arizona',
				'tax_number'    => 100022,
				'default'       => 0
			],
		];

		for ( $i = 0; $i < count($taxes); $i++ ) {
			$wpdb->insert( $table, [
				'tax_rate_name' => $taxes[$i]['tax_rate_name'],
				'tax_number'    => $taxes[$i]['tax_number'],
				'default'       => $taxes[$i]['default']
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
	 * erp_people_types
	 */
	private function _seed_erp_people_types( $table_name ) {
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
