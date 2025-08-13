<?php

class QuickCal_WC {

	private function __construct() {
		if ( $this->check_plugin_dependencies() ) {

			$this->setup_woocommerce_support();
			$this->setup_booked_custom_fields();
			$this->enqueue_scripts();
			$this->wp_ajax();
			$this->filters_and_actions();
			$this->add_new_post_status();
			$this->add_options_pages();
			$this->setup_wp_cron();

		}
	}

	public static function setup() {
		return new self();
	}

	protected function check_plugin_dependencies() {
		return class_exists('woocommerce');
	}

	protected function filters_and_actions() {

		# ------------------
		# Filters
		# ------------------

		add_filter('booked_mailer_actions', array('QuickCal_WC_Functions', 'booked_wc_mailer_actions'), 10, 1 );
		add_filter('booked_prepare_sending_reminder', array('QuickCal_WC_Functions', 'booked_prepare_sending_reminder'), 10, 2);
		add_filter('booked_custom_field_data', array('QuickCal_WC_Functions', 'booked_custom_field_data'), 10, 1);
                //temporary cleanup of the appointmens due to missing orders
                add_filter('booked_appointments_array', array('QuickCal_WC_Functions', 'booked_appointments_array_cleanup'), 10, 1);
		add_filter('booked_appointments_array', array('QuickCal_WC_Functions', 'booked_appointments_array'), 10, 1);
		add_filter('booked_shortcode_appointments_allow_cancel', array('QuickCal_WC_Functions', 'booked_shortcode_appointments_allow_cancel'), 10, 2);
		add_filter('booked_admin_pending_post_status',array('QuickCal_WC_Functions', 'booked_admin_pending_post_status'), 10, 2);
		add_filter('booked_fea_shortcode_appointments_buttons', array('QuickCal_WC_Functions', 'booked_fea_shortcode_appointments_buttons'), 10, 2);
		add_filter('woocommerce_cart_item_name', array('QuickCal_WC_Cart_Hooks', 'woocommerce_cart_item_name'), 10, 3);
		add_filter('woocommerce_cart_item_permalink', array('QuickCal_WC_Cart_Hooks', 'woocommerce_cart_item_permalink'), 10, 3);
		add_filter('woocommerce_cart_item_thumbnail', array('QuickCal_WC_Cart_Hooks', 'woocommerce_cart_item_thumbnail'), 10, 2 );
		add_filter('woocommerce_checkout_cart_item_quantity', array('QuickCal_WC_Cart_Hooks', 'woocommerce_checkout_cart_item_quantity'), 10, 3 );
		add_filter('woocommerce_order_item_name', array('QuickCal_WC_Order_Item_Hooks', 'woocommerce_order_item_name'), 10, 2);
		add_filter('woocommerce_attribute_label', array('QuickCal_WC_Order_Item_Hooks', 'woocommerce_attribute_label'), 10, 3);
		add_filter('woocommerce_hidden_order_itemmeta', array('QuickCal_WC_Order_Hooks', 'woocommerce_hidden_order_itemmeta'), 10);
		add_filter('woocommerce_order_items_meta_display', array('QuickCal_WC_Order_Hooks', 'woocommerce_order_items_meta_display'), 10, 2);
		add_filter('woocommerce_checkout_fields', array('QuickCal_WC_Cart_Hooks', 'woocommerce_checkout_fields'), 10, 1 );

		# ------------------
		# Actions
		# ------------------

		add_action('wp_loaded', array('QuickCal_WC_Cart_Hooks', 'woocommerce_remove_missing_appointment_products'), 10, 1);
		add_action('woocommerce_resume_order', array('QuickCal_WC_Order_Hooks', 'woocommerce_validate_order_items'), 10, 1);
		add_action('woocommerce_new_order', array('QuickCal_WC_Order_Hooks', 'woocommerce_validate_order_items'), 10, 1);
		add_action('booked_new_appointment_created', array('QuickCal_WC_Functions', 'booked_new_appointment_created'), 15, 1);
		add_action('booked_new_appointment_created', array('QuickCal_WC_Functions', 'booked_store_appointment_creation_date'), 10, 1);
		add_action('booked_before_creating_appointment', array('QuickCal_WC_Functions', 'remove_default_emails'), 1);
		add_action('booked_before_creating_appointment', array('QuickCal_WC_Functions', 'booked_before_creating_appointment'), 10);

		// On Order Complete
		add_action('woocommerce_order_status_completed', array('QuickCal_WC_Order_Hooks', 'woocommerce_order_complete'), 10, 1);

		// Trash the appointment on order cancel, refunded or deleted
		add_action('woocommerce_order_status_changed', array('QuickCal_WC_Order_Hooks', 'woocommerce_order_status_change'), 10, 3);
                
		add_action('wp_trash_post', array('QuickCal_WC_Order_Hooks', 'woocommerce_order_remove_appointment'), 10, 1);
                add_action('wp_delete_post', array('QuickCal_WC_Order_Hooks', 'woocommerce_order_remove_appointment'), 10, 1);

                //if HPOS is enabled
                add_action('woocommerce_before_trash_order', array('QuickCal_WC_Order_Hooks', 'woocommerce_before_trash_order'));
                add_action('woocommerce_before_delete_order', array('QuickCal_WC_Order_Hooks', 'woocommerce_before_delete_order'), 10, 2);

                
		add_action('booked_admin_calendar_buttons_after', array('QuickCal_WC_Functions', 'booked_admin_calendar_buttons_after'), 10, 3);
		add_action('booked_admin_calendar_buttons_before', array('QuickCal_WC_Functions', 'booked_admin_calendar_buttons_before'), 10, 3);
		add_action('booked_shortcode_appointments_buttons', array('QuickCal_WC_Functions', 'booked_shortcode_appointments_buttons'), 10, 1);
		add_action('booked_shortcode_appointments_additional_information', array('QuickCal_WC_Functions', 'booked_shortcode_appointments_additional_information'), 10, 1);
		add_action('woocommerce_new_order_item', array('QuickCal_WC_Order_Hooks', 'woocommerce_new_order_item'), 10, 2);
                
                add_filter('booked_check_cancel_button_conditions', array('QuickCal_WC_Order_Hooks', 'woocommerce_check_cancel_button_conditions'), 10, 2);

	}

	protected function setup_woocommerce_support() {
		QuickCal_WC_Main::setup();
	}

	protected function setup_booked_custom_fields() {
		QuickCal_WC_Custom_Fields::setup();
	}

	protected function enqueue_scripts() {
		QuickCal_WC_EnqueueScript::enqueue();
	}

	protected function wp_ajax() {
		QuickCal_WC_Ajax::setup();
	}

	protected function add_new_post_status() {
		QuickCal_WC_Post_Status::setup();
	}

	protected function add_options_pages() {
		QuickCal_WC_Settings::setup();
	}

	protected function setup_wp_cron() {
		QuickCal_WC_WP_Crons::setup();
	}
}
