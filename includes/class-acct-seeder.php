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
		$this->limit = 10;

		$this->get_tables_name();
		$this->drop_db_tables();
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
	 * DROP tables data before insert
	 */
	private function drop_db_tables() {
		global $wpdb;

		foreach ($this->tables as $table) {
			$wpdb->query('DROP TABLE ' . $wpdb->prefix . $table);
		}

		// shell_exec('wp plugin toggle wp-erp');
		// shell_exec('wp plugin toggle wp-erp');
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

			return $id;
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
	private function seed_erp_acct_chart_of_accounts( $table_name ) {
		global $wpdb;
		$table = $wpdb->prefix . $table_name;

		$charts = ['Asset', 'Liability', 'Equity', 'Income', 'Expense', 'Asset & Liability', 'Bank'];

		// insert
		for ( $i = 0; $i < count($charts); $i++ ) {
			$wpdb->insert( $table, [ 'name' => $charts[$i], 'slug' => $this->slugify($charts[$i]) ] );
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
	 * erp_acct_ledgers
	 */
	private function seed_erp_acct_ledgers( $table_name ) {
		global $wpdb;
		$table = $wpdb->prefix . $table_name;

		$ledgers = [
			'asset' => [
				'Cash',
				'Bank Balance',
				'Accounts Receivable',
				'Allowance for Doubtful Accounts',
				'Interest Receivable',
				'Inventory',
				'Supplies',
				'Prepaid Insurance',
				'Prepaid Rent',
				'Prepaid Salary',
				'Land',
				'Equipment',
				'Furniture & Fixture',
				'Buildings',
				'Copyrights',
				'Goodwill',
				'Patents',
				'Accoumulated Depreciation- Equipment',
				'Accoumulated Depreciation- Buildings',
				'Accoumulated Depreciation- Furniture & Fixture'
			],
			
			'liability' => [
				'Notes Payable',
				'Accounts Payable',
				'Unearned Revenue',
				'Salaries and Wages Payable',
				'Unearned Rent Revenue',
				'Interest Payable',
				'Dividends Payable',
				'Income Tax Payable',
				'Sales Tax Payable',
				'Bonds Payable',
				'Discount on Bonds Payable',
				'Pfemium on Bonds Payable',
				'Mortgage Payable'
			],

			'equity' => [
				'Owner\'s Capital',
				'Owner\'s  Drawings',
				'Common Stock',
				'Paid- in Capital in Excess of Par- Common Stock',
				'Paid- in Capital in Excess of Par- Preferred  Stock',
				'Preferred Stock',
				'Treasury Stock',
				'Retained Earnings',
				'Dividends',
				'Income Summary'
			],

			'income' => [
				'Service Revenue',
				'Sales Revenue',
				'Sales Discounts',
				'Sales Returns and Allowance',
				'Interest Revenue',
				'Gain on Disposal of Plant Assets'
			],

			'expense' => [
				'Advertising Expense',
				'Amortization Expense',
				'Bad Debt Expense',
				'Cost of Goods Sold',
				'Depreciation Expense',
				'Freight -Out',
				'Income Tax Expense',
				'Insurance Expense',
				'Interest Expense',
				'Loss on Disposal of Plant Assets',
				'Maintenance and Repairs Expense',
				'Salaries and  wages Expense',
				'Rent Expense',
				'Supplies Expense',
				'Utilites Expense'
			]
		];

		foreach ( array_keys( $ledgers ) as $array_key ) {
			foreach ( $ledgers[$array_key] as $value ) {
				$wpdb->insert( 
					$table,
					[ 
						'chart_id' => $this->get_chart_id_by_slug($array_key),
						'name'     => $value,
						'slug'     => $this->slugify($value),
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
					'first_name' => $this->faker->firstName,
					'last_name'  => $this->faker->lastName,
					'email'      => $this->faker->email
				]
			);
		}
	}

}