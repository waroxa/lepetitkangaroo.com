<?php

if(!class_exists('Booked_AJAX')) {
	class Booked_AJAX {

		public function __construct() {

			// ------------ Guests & Logged-in Users ------------ //

				// Actions
                        add_action('wp_ajax_booked_get_new_nonce_after_login', array(&$this,'booked_get_new_nonce_after_login'));
                        
			add_action('wp_ajax_booked_ajax_login', array(&$this,'booked_ajax_login'));
			add_action('wp_ajax_nopriv_booked_ajax_login', array(&$this,'booked_ajax_login'));

			add_action('wp_ajax_booked_ajax_forgot', array(&$this,'booked_ajax_forgot'));
			add_action('wp_ajax_nopriv_booked_ajax_forgot', array(&$this,'booked_ajax_forgot'));

			add_action('wp_ajax_booked_add_appt', array(&$this,'booked_add_appt'));
			add_action('wp_ajax_nopriv_booked_add_appt', array(&$this,'booked_add_appt'));

				// Loaders

			add_action('wp_ajax_booked_calendar_month', array(&$this,'booked_calendar_month'));
			add_action('wp_ajax_nopriv_booked_calendar_month', array(&$this,'booked_calendar_month'));

			add_action('wp_ajax_booked_calendar_date', array(&$this,'booked_calendar_date'));
			add_action('wp_ajax_nopriv_booked_calendar_date', array(&$this,'booked_calendar_date'));

			add_action('wp_ajax_booked_appointment_list_date', array(&$this,'booked_appointment_list_date'));
			add_action('wp_ajax_nopriv_booked_appointment_list_date', array(&$this,'booked_appointment_list_date'));

			add_action('wp_ajax_booked_new_appointment_form', array(&$this,'booked_new_appointment_form'));
			add_action('wp_ajax_nopriv_booked_new_appointment_form', array(&$this,'booked_new_appointment_form'));

                        add_action('booked_before_creating_appointment', array(&$this, 'booked_before_updating_appointment'), 10);

			// ------------ Logged-in Users Only ------------ //

				// Actions

			add_action('wp_ajax_booked_cancel_appt', array(&$this,'booked_cancel_appt'));

		}
		
		public static function nonce_check( $nonce ){
			if ( !wp_verify_nonce( $_POST['nonce'], $nonce ) ){
				die ( 'Required "nonce" value is not here, please let the developer know.');
			}
		}
                
                public static function booked_get_new_nonce_after_login(){
                    $user = wp_get_current_user();
                    $response = ['nonce'=> wp_create_nonce('ajax-nonce'), 'userId'=> $user->ID];
                    wp_send_json_success($response);
                }

		// ------------ LOADERS ------------ //

		// Calendar Month
		public function booked_calendar_month(){
			
			Booked_AJAX::nonce_check( 'ajax-nonce' );

			booked_wpml_ajax();

			if (isset($_POST['gotoMonth'])):

				$calendar_id = (isset($_POST['calendar_id']) ? $_POST['calendar_id'] : false);
				$force_default = (isset($_POST['force_default']) ? $_POST['force_default'] : false);
				$timestamp = ($_POST['gotoMonth'] != 'false' ? strtotime($_POST['gotoMonth']) : current_time('timestamp'));

				$year = date_i18n('Y',$timestamp);
				$month = date_i18n('m',$timestamp);

				booked_fe_calendar($year,$month,$calendar_id,$force_default);

			endif;
			wp_die();

		}

		// Calendar Date
		public function booked_calendar_date(){
			
			Booked_AJAX::nonce_check( 'ajax-nonce' );

			booked_wpml_ajax();

			if (isset($_POST['date'])):

				$calendar_id = (isset($_POST['calendar_id']) ? $_POST['calendar_id'] : false);
				booked_fe_calendar_date_content($_POST['date'],$calendar_id);

			endif;
			wp_die();

		}

		// Appointment List Date
		public function booked_appointment_list_date(){
			
			Booked_AJAX::nonce_check( 'ajax-nonce' );

			booked_wpml_ajax();

			if (isset($_POST['date'])):

				$date = date_i18n('Ymd',strtotime($_POST['date']));
				$calendar_id = (isset($_POST['calendar_id']) ? $_POST['calendar_id'] : false);
				$force_default = (isset($_POST['force_default']) ? $_POST['force_default'] : false);

				booked_fe_appointment_list_content($date,$calendar_id,$force_default);

			endif;
			wp_die();

		}
                
                public static function booked_before_updating_appointment() {

                        if ( is_admin() && isset($_POST['booked_form_type']) && $_POST['booked_form_type'] == 'admin' ):
                                return;
                        endif;

                        $action = (isset($_POST['action']) &&!empty($_POST['action'])) ? $_POST['action'] : false;
                        $app_id = (isset($_POST['app_id']) &&!empty($_POST['app_id'])) ? $_POST['app_id'] : false;
                        $app_action = (isset($_POST['app_action']) &&!empty($_POST['app_action'])) ? $_POST['app_action'] : false;

                        if (
                                (!$action || $action !== 'booked_add_appt')
                                || (!$app_id || !intval($app_id))
                                || (!$app_action || $app_action!=='edit')
                        ) {
                                return;
                        }

                        $current_time = current_time('timestamp');
                        $timestamp = get_post_meta( $app_id,'_appointment_timestamp',true);
                        // check if the date has been passed
                        if ( $current_time > $timestamp ) {
                                return;
                        }

                        // turn the wp_insert_post to act as wp_update_post
                        add_filter('booked_new_appointment_args', array('Booked_AJAX', 'booked_new_appointment_args_on_date_change'), 10, 1);

                        // dont allow to update the metas and the calendar term
                        add_filter('booked_update_cf_meta_value', array('Booked_AJAX', 'return_false'), 10);
                        add_filter('booked_update_appointment_calendar', array('Booked_AJAX', 'return_false'), 10);

                }
                
                public static function return_false() {
                        return false;
                }
                
		// New Appointment Form
		public function booked_new_appointment_form(){
			
			Booked_AJAX::nonce_check( 'ajax-nonce' );

			booked_wpml_ajax();

			if ( apply_filters( 'booked_show_new_appointment_form', true ) ):
                            if (
                                !empty($_POST['app_action']) && $_POST['app_action'] === 'edit'
                            ) {
                                include(QUICKCAL_AJAX_INCLUDES_DIR . 'front/appointment-change-date.php');
                            } else {
                                include(QUICKCAL_AJAX_INCLUDES_DIR . 'front/appointment-form.php');
                            }


            endif;

			wp_die();
		}


		// ------------ ACTIONS ------------ //

		public function booked_ajax_login(){
			
			booked_wpml_ajax();

			if (isset($_POST['security']) && isset($_POST['username']) && isset($_POST['password'])):

				$nonce_check = wp_verify_nonce( $_POST['security'], 'ajax_login_nonce' );

				if ($nonce_check){

					if (is_email($_POST['username'])) {
				        $user = get_user_by('email', $_POST['username']);
				    } else {
						$user = get_user_by('login', $_POST['username']);
				    }

				    $creds = array();

				    if ($user && wp_check_password( $_POST['password'], $user->data->user_pass, $user->ID)) {
				        $creds = array('user_login' => $user->data->user_login, 'user_password' => $_POST['password']);
				        $creds['remember'] = true;
				    }
                                    
					$user = wp_signon( $creds, false );
					if ( !is_wp_error($user) ):
                                                //set the user explicitly
                                                wp_set_current_user($user->ID);
						echo 'success';
                                    endif;

				}

			endif;

			wp_die();

		}

		public function booked_ajax_forgot(){

			booked_wpml_ajax();

			global $wpdb, $wp_hasher;

			if (isset($_POST['security']) && isset($_POST['username'])):

				$nonce_check = wp_verify_nonce( $_POST['security'], 'ajax_forgot_nonce' );

				if ($nonce_check){

					$password_reset = booked_reset_password( $_POST['username'] );
                                        
					if ( $password_reset ):
						echo 'success';
					endif;

				}

			endif;

			wp_die();

		}
                
                // see wp_update_post()
                public static function booked_new_appointment_args_on_date_change( $post_args ) {

                        $appointment_id = $_POST['app_id'];
                        $appointment_obj = get_post($appointment_id, ARRAY_A);

                        if ( !$appointment_obj ) {
                                return 0;
                        }

                        $default_post_status = get_option('booked_new_appointment_default','draft');
                        $post_args['ID'] = $_POST['app_id'];

                        // Escape data pulled from DB.
                        $appointment_obj = wp_slash($appointment_obj);

                        // Passed post category list overwrites existing category list if not empty.
                        $post_cats = $appointment_obj['post_category'];
                        $post_args = array_merge($appointment_obj, $post_args);
                        $post_args['post_category'] = $post_cats;

                        // Drafts shouldn't be assigned a date unless explicitly done so by the user.
                        $post_args['post_date'] = current_time('mysql');
                        $post_args['post_date_gmt'] = '';

                        $post_args['post_status'] = $default_post_status;

                        return $post_args;

                }
                
		public function booked_add_appt(){
			
			Booked_AJAX::nonce_check( 'ajax-nonce' );

			booked_wpml_ajax();

			$can_add_appt = apply_filters(
				'booked_can_add_appt',
				isset($_POST['date']) && isset($_POST['timestamp']) && isset($_POST['timeslot']) && isset($_POST['customer_type'])
			);

			if ( $can_add_appt ):

				include(QUICKCAL_AJAX_INCLUDES_DIR . 'front/book-appointment.php');

			endif;

			wp_die();

		}

		public function booked_cancel_appt(){
			
			Booked_AJAX::nonce_check( 'ajax-nonce' );

			booked_wpml_ajax();

			if (is_user_logged_in() && isset($_POST['appt_id'])):

				include(QUICKCAL_AJAX_INCLUDES_DIR . 'front/cancel-appointment.php');

			endif;

			wp_die();

		}



	}
}
