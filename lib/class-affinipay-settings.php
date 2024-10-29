<?php



namespace AffiniPayWordPress;

class AffiniPay_Settings {
	var $instance;
	var $operating_mode;
	var $receipt_page;
    var $secret_key;
	var $test_public_key;
	var $test_secret_key;
	var $test_account;
	var $live_public_key;
	var $live_secret_key;
	var $live_account;
	var $ui;
	var $affinipay_client;
	var $file;
	var $plugin_name;

	function __construct($file) {
	    $this->file = $file;
        $this->plugin_name = explode('/', plugin_basename($file))[0];
		add_action( 'admin_menu', array( $this, 'add_admin_menu_page' ) );
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'admin_footer', array( $this, 'enqueue_admin_ui_scripts' ) );
		add_action( 'admin_head', array( $this, 'enqueue_admin_ui_styles' ) );
		add_action( 'wp_ajax_affinipay_settings_submit', array( $this, 'save' ) );
		add_action( 'wp_ajax_affinipay_api_test', array( $this, 'perform_api_test' ) );
        add_action( 'wp_ajax_affinipay_select_accounts', array( $this, 'select_accounts' ) );
		add_filter( 'plugin_action_links', array( $this, 'add_action_links' ), 10, 5 );

		$this->affinipay_client = new AffiniPay_Client();
	}

	function admin_init() {
		$this->get_options();
		$this->load_scripts();
		$this->load_styles();
	}

	function load_scripts() {
		if ( ! wp_script_is( 'affinipay-admin-scripts', 'enqueued' ) ) {
			wp_enqueue_script( 'affinipay-admin-scripts' );
		}
	}

	function load_styles() {
		if ( ! wp_style_is( 'affinipay-admin-styles', 'enqueued' ) ) {
			wp_enqueue_style( 'affinipay-admin-styles' );
		}
	}

	function add_admin_menu_page() {
		add_options_page(
			'AffiniPay Payments',
			'Affinipay Payments',
			'administrator',
			__FILE__,
			array( $this, 'display_settings_page' )
		);
	}

	function get_options() {
		$this->operating_mode = get_option( 'affinipay_operating_mode' );
        $this->secret_key = get_option( 'affinipay_secret_key' );
        $this->receipt_page = get_option( 'affinipay_receipt_page' );
	}

	function add_action_links( $links, $source ) {
        $myLinks = array();
        if ($source == plugin_basename($this->file)) {
            $myLinks = array(
                '<a href="' . admin_url('options-general.php?page='. $this->plugin_name .'/lib/class-affinipay-settings.php') . '">Settings</a>'
            );
        }

        return array_merge( $links, $myLinks );
	}

	function display_settings_page() {

		$verify_test = 'none';
		$verify_live = 'none';

		if ( $this->test_public_key != '' && $this->test_secret_key != '' && $this->test_account != '' ) {
			$verify_test = 'block';
		}

		if ( $this->live_public_key != '' && $this->live_secret_key != '' && $this->live_account != '' ) {
			$verify_live = 'block';
		}

		include_once( AFFINIPAY_PLUGIN_DIR . '/views/admin-settings.php' );
	}

	public function getDefault($key, $default = null){
	    return array_key_exists( $key, $_POST[$key] ) ? $_POST[$key] : $default;
    }

	function save() {

		$verify_test = false;
		$verify_live = false;

		$ap_page = null;
        $secret_key = $_POST['secret_key'];
        $public_key = $_POST['public_key'];
		$receipt_page = $_POST['receipt_page'];

		if ( $receipt_page == 'new' ) {

		} else {
			$ap_page = get_post( $receipt_page );
		}

		$redirect_url = ($ap_page instanceof \WP_Post ? $ap_page->ID : null);

		update_option( 'affinipay_receipt_page', $redirect_url );
        update_option( 'affinipay_secret_key', $secret_key );
        update_option( 'affinipay_public_key', $public_key );

		ob_start();

		$this->select_accounts();

		wp_die();  // Terminate the AJAX request
	}

	function enqueue_admin_ui_scripts() {
		if ( ! wp_script_is( 'affinipay-admin-scripts', 'enqueued' ) ) {
			wp_enqueue_script( 'affinipay-admin-scripts' );
		}
	}

	function enqueue_admin_ui_styles() {
		if ( ! wp_style_is( 'affinipay-admin-styles', 'enqueued' ) ) {
			wp_enqueue_style( 'affinipay-admin-styles' );
		}
	}

	function select_accounts(){
        $this->affinipay_client->public_key = $_POST['public_key'];
        $this->affinipay_client->secret_key = $_POST['secret_key'];
        $this->affinipay_client->set_credentials();
        try {
            $merchant_accounts = $this->affinipay_client->getMerchant()->attributes['merchant_accounts'];
        }
        catch(\ChargeIO_Error $e){
            include( AFFINIPAY_PLUGIN_DIR . '/views/admin-settings-default.php' );
            return;
        }

        include( AFFINIPAY_PLUGIN_DIR . '/views/admin-accounts-response.php' );
    }

    function perform_api_test()
    {
        $api_test_errors = [];

        if (!array_key_exists('credentials', $_POST)) {
            $errors = array(AffiniPay_Error_Message::AUTHENTICATION_FAILURE);
            $json = json_encode($errors);
            wp_die();
            return;
        }

        if ($_POST['mode'] == "live") {
            $this->affinipay_client->set_operating_mode('live');
        }

        $this->affinipay_client->account_id = $_POST['credentials']['account'];
        $this->affinipay_client->public_key = $_POST['credentials']['public_key'];
        $this->affinipay_client->secret_key = $_POST['credentials']['secret_key'];
        $this->affinipay_client->set_credentials();

        try {
            $this->perform_public_key_test();
        } catch (AffiniPay_Error $e) {
            $api_test_errors['public'] = $e->getMessage();
        }

        try {
            $secret_key_results = $this->affinipay_client->getMerchant();
        } catch (AffiniPay_Error $e) {
            $api_test_errors['secret'] = $e->getMessage();
        }

        try {
            $account_id_results = $this->affinipay_client->isValidAccount();
        } catch (AffiniPay_Error $e) {
            $api_test_errors['account'] = $e->getMessage();
        }

        include_once(AFFINIPAY_PLUGIN_DIR . '/views/admin-test-response.php');


        wp_die();
    }

	private function perform_public_key_test() {
	    return $this->affinipay_client->createToken(array(
            'amount' => 100,
            'method' => array(),
        ));
	}

	function create_receipt_page() {

		$find_args = array(
			'post_type' => 'page',
			'post_status' => 'publish',
			'name' => 'payment-receipt',
		);

		$existing = get_posts( $find_args );

		if ( $existing ) {
			$receipt_page = $existing;
		} else {
			$ap_page = array(
				'post_type' => 'page',
				'post_title' => 'Payment Receipt',
				'post_content' => '[affinipay-receipt]',
				'post_status' => 'publish',
				'post_author' => get_current_user_id(),
			);

			$receipt_page = wp_insert_post( $ap_page );
			if ( is_wp_error( $receipt_page ) ) {
				echo '<pre>';
				print_r( $receipt_page );
				echo '<pre>';
			} else {
				$receipt_data = get_post( $receipt_page );
			}
		}

		return $receipt_page;
	}


	function plugin_deactivate() {
		delete_option( 'affinipay_mode' );
		delete_option( 'affinipay_receipt_page' );
		delete_option( 'affinipay_test_public_key' );
		delete_option( 'affinipay_test_secret_key' );
		delete_option( 'affinipay_test_account_id' );
		delete_option( 'affinipay_live_public_key' );
		delete_option( 'affinipay_live_secret_key' );
		delete_option( 'affinipay_live_account_id' );
	}
}
