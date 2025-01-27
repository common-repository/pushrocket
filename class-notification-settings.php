<?php
namespace Pushrocket;

if ( ! class_exists( 'Notification_Settings', false ) ) {
	/**
	 * Class Notification_Settings.
	 *
	 * This class handles the notification settings functionality for the Pushrocket plugin.
	 * It provides methods for registering settings, rendering settings fields, and handling data processing.
	 */
	class Notification_Settings {
		/**
		 * Constructor for the Pushrocket Plugin.
		 *
		 * This constructor initializes the Pushrocket plugin by setting up the necessary actions.
		 * and filters for managing settings and user interactions within the WordPress admin panel.
		 */
		public function __construct() {
			add_action( 'admin_menu', array( $this, 'pushrocket_register_settings' ) );
			add_action( 'admin_init', array( $this, 'pushrocket_register_setting' ) );
			add_filter( 'sanitize_option_pushrocket_panel_url', array( $this, 'pushrocket_settings_pre_save' ) );
			$show_multiple_site = get_transient( 'pushrocket_show_multiple_site' );
			if ( 'yes' === $show_multiple_site ) {
				add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
				add_filter( 'post_row_actions', array( $this, 'add_post_row_actions' ), 10, 2 );
			}
		}
		/**
		 * Register the Pushrocket settings.
		 *
		 * This function is responsible for registering the settings used by the Pushrocket plugin.
		 * It defines the settings group, settings fields, and their respective callbacks.
		 */
		public function pushrocket_register_setting() {

			register_setting(
				'pushrocket_settings', // settings group name.
				'pushrocket_website_lists', // option name.
			);
			register_setting(
				'pushrocket_settings', // settings group name.
				'pushrocket_api_url', // option name.
			);
			register_setting(
				'pushrocket_settings', // settings group name.
				'pushrocket_panel_url', // option name.
			);
			register_setting(
				'pushrocket_settings', // settings group name.
				'pushrocket_website_code', // option name.
			);
			register_setting(
				'pushrocket_settings', // settings group name.
				'pushrocket_username', // option name.
			);
			register_setting(
				'pushrocket_settings', // settings group name.
				'pushrocket_password', // option name.
			);
			register_setting(
				'pushrocket_settings', // settings group name.
				'pushrocket_push_on_publish', // option name.
			);
			register_setting(
				'pushrocket_settings', // settings group name.
				'pushrocket_push_on_publish_for_webstories', // option name.
			);
			add_settings_section(
				'some_settings_section_id', // section id.
				'', // title (if needed).
				'', // callback function (if needed).
				'pushrocket' // page slug.
			);
			$show_multiple_site = get_transient( 'pushrocket_show_multiple_site' );
			if ( 'yes' === $show_multiple_site ) {
				add_settings_field(
					'pushrocket_website_lists',
					__( 'Website Lists', 'pushrocket' ),
					array(
						$this,
						'pushrocket_website_lists_field_html',
					),
					'pushrocket', // page slug.
					'some_settings_section_id', // section id.
					array(
						'label_for' => 'content_type',
						'class'     => '',
					)
				);
			}
			add_settings_field(
				'pushrocket_panel_url',
				'Panel URL',
				array(
					$this,
					'pushrocket_panel_url_field_html',
				),
				'pushrocket', // page slug.
				'some_settings_section_id', // section id.
				array(
					'label_for' => 'content_type',
					'class'     => '',
				)
			);
			add_settings_field(
				'pushrocket_api_url',
				'API url without /',
				array(
					$this,
					'pushrocket_api_url_field_html',
				),
				'pushrocket', // page slug.
				'some_settings_section_id', // section id.
				array(
					'label_for' => 'content_type',
					'class'     => '',
				)
			);
			add_settings_field(
				'pushrocket_website_code',
				'Website code',
				array(
					$this,
					'pushrocket_website_code_field_html',
				),
				'pushrocket', // page slug.
				'some_settings_section_id', // section id.
				array(
					'label_for' => 'content_type',
					'class'     => '',
				)
			);
			add_settings_field(
				'pushrocket_username',
				__( 'User Name', 'pushrocket' ),
				array(
					$this,
					'pushrocket_username_field_html',
				),
				'pushrocket', // page slug.
				'some_settings_section_id', // section id.
				array(
					'label_for' => 'content_type',
					'class'     => '',
				)
			);
			add_settings_field(
				'pushrocket_password',
				__( 'Password', 'pushrocket' ),
				array(
					$this,
					'pushrocket_password_field_html',
				),
				'pushrocket', // page slug.
				'some_settings_section_id', // section id.
				array(
					'label_for' => 'content_type',
					'class'     => '',
				)
			);
			add_settings_field(
				'pushrocket_push_on_publish',
				__( 'Enable Automatic Push on Publish', 'pushrocket' ),
				array(
					$this,
					'pushrocket_push_on_publish_field_html',
				),
				'pushrocket', // page slug.
				'some_settings_section_id', // section id.
				array(
					'label_for' => 'content_type',
					'class'     => '',
				)
			);
			add_settings_field(
				'pushrocket_push_on_publish_for_webstories',
				__( 'Enable Automatic Push on Publish Webstories', 'pushrocket' ),
				array(
					$this,
					'pushrocket_push_on_publish_for_webstories_field_html',
				),
				'pushrocket', // page slug.
				'some_settings_section_id', // section id.
				array(
					'label_for' => 'content_type',
					'class'     => '',
				)
			);

		}
		/**
		 * Render the HTML field for Pushrocket panel URL.
		 */
		public function pushrocket_panel_url_field_html() {
			$site_url                  = get_site_url(); // Get the complete site URL.
			$site_url_without_protocol = str_replace( array( 'http://', 'https://' ), '', $site_url );
			printf(
				'<input type="text" id="pushrocket_panel_url" name="pushrocket_panel_url" value="%s" readonly=true />',
				esc_attr( $site_url_without_protocol )
			);
		}
		/**
		 * Render the HTML field for Pushrocket website code.
		 */
		public function pushrocket_website_code_field_html() {
			printf(
				'<input type="text" id="pushrocket_website_code" name="pushrocket_website_code" value="%s" />',
				esc_attr( get_option( 'pushrocket_website_code' ) )
			);
		}
		/**
		 * Render the HTML field for Pushrocket api url.
		 */
		public function pushrocket_api_url_field_html() {
			printf(
				'<input type="text" id="pushrocket_api_url" name="pushrocket_api_url" value="%s" />',
				esc_attr( get_option( 'pushrocket_api_url' ) )
			);
		}
		/**
		 * Render the HTML field for Pushrocket username.
		 */
		public function pushrocket_username_field_html() {
			printf(
				'<input type="text" id="pushrocket_username" name="pushrocket_username" value="%s" />',
				esc_attr( get_option( 'pushrocket_username' ) )
			);
		}
		/**
		 * Render the HTML field for Pushrocket password.
		 */
		public function pushrocket_password_field_html() {
			printf(
				'<input type="password" id="pushrocket_password" name="pushrocket_password" value="%s" />',
				esc_attr( get_option( 'pushrocket_password' ) )
			);
		}
		/**
		 * Render the HTML field for Pushrocket push on publish option.
		 */
		public function pushrocket_push_on_publish_field_html() {
			printf(
				'<input type="checkbox" id="pushrocket_push_on_publish" name="pushrocket_push_on_publish" value="1" %s />',
				checked(
					1,
					get_option( 'pushrocket_push_on_publish', 0 ),
					false
				)
			);
		}
		/**
		 * Render the HTML field for Pushrocket push on publish option for web stories.
		 */
		public function pushrocket_push_on_publish_for_webstories_field_html() {
			$pushrocket_website_lists = get_option( 'pushrocket_website_lists' );
			printf(
				'<input type="checkbox" id="pushrocket_push_on_publish_for_webstories" name="pushrocket_push_on_publish_for_webstories" value="1" %s />',
				checked(
					1,
					get_option( 'pushrocket_push_on_publish_for_webstories', 0 ),
					false
				)
			);
		}
		/**
		 * Render the HTML field for Pushrocket website lists.
		 */
		public function pushrocket_website_lists_field_html() {
			$pushrocket_panel_url     = get_option( 'pushrocket_panel_url' );
			$pushrocket_website_code  = get_option( 'pushrocket_website_code' );
			$pushrocket_username      = get_option( 'pushrocket_username' );
			$pushrocket_password      = get_option( 'pushrocket_password' );
			$pushrocket_website_lists = get_option( 'pushrocket_website_lists' );
			$pushrocket_api_url       = get_option( 'pushrocket_api_url' );
			$data_to_send             = array(
				'WebsiteURL'  => $pushrocket_panel_url,
				'WebsiteCode' => $pushrocket_website_code,
				'UserName'    => $pushrocket_username,
				'Password'    => $pushrocket_password,
			);
			// Perform wp_remote_post with all form values.
			$response = wp_remote_post(
				$pushrocket_api_url . '/api/User/GetWebsiteList',
				array(
					'body' => $data_to_send, // Pass the form data here.
				)
			);
			if ( is_array( $response ) ) {
				$response_body    = wp_remote_retrieve_body( $response ); // Response body.
				$decoded_response = json_decode( $response_body, true );
				foreach ( $decoded_response['Data'] as $website_data ) {
					$item_name = $website_data['WebsiteName'];
					$item_id   = (string) $website_data['Id'];
					$checked   = ( ! empty( $pushrocket_website_lists ) && in_array( $item_id, $pushrocket_website_lists, true ) ) ? ' checked="checked" ' : '';
					echo '<label><input ' . esc_attr( $checked ) . " value='" . esc_attr( $item_id ) . "' name='pushrocket_website_lists[]' type='checkbox' /> " . esc_html( $item_name ) . '</label><br />';
				}
			}
		}
		/**
		 * Register the Pushrocket settings page.
		 */
		public function pushrocket_register_settings() {
			add_menu_page(
				__( 'Pushrocket', 'pushrocket' ), // page <title>Title</title>.
				__( 'Pushrocket', 'pushrocket' ), // menu link text.
				'manage_options',
				'pushrocket', // page URL slug.
				array(
					$this,
					'pushrocket_content',
				),
				plugin_dir_url( __FILE__ ) . 'images/pushrocket-icon.png',
				25
			);
		}
		/**
		 * Display the Pushrocket settings content.
		 */
		public function pushrocket_content() {
			echo '<div class="wrap"><h1>';
			esc_html_e( 'Pushrocket', 'pushrocket' );
			echo '</h1>';
			echo '<h3>';
			esc_html_e( 'Send unlimited push notifications to your website/blog users directly from WordPress Dashboard.', 'pushrocket' );
			echo '</h3>
			 <form method="post" action="options.php" name="pushrocket_settings_data">';
				wp_nonce_field( 'save_pushrocket_setting', 'pushrocket_nonce' );
				settings_fields( 'pushrocket_settings' ); // settings group name.
				do_settings_sections( 'pushrocket' ); // just a page slug.
				submit_button();
			echo '</form></div>';
		}
		/**
		 * Pre-save function for Pushrocket settings.
		 *
		 * @param mixed $value The current value being saved.
		 *
		 * @return mixed The modified or original value to be saved in the database.
		 */
		public function pushrocket_settings_pre_save( $value ) {
			// Get the submitted form data.
			$pushrocket_nonce = isset( $_POST['pushrocket_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['pushrocket_nonce'] ) ) : '';
			if ( empty( $pushrocket_nonce ) || ! wp_verify_nonce( $pushrocket_nonce, 'save_pushrocket_setting' ) ) {
				return;
			}
			$pushrocket_panel_url    = isset( $_POST['pushrocket_panel_url'] ) ? sanitize_text_field( wp_unslash( $_POST['pushrocket_panel_url'] ) ) : '';
			$pushrocket_website_code = isset( $_POST['pushrocket_website_code'] ) ? sanitize_text_field( wp_unslash( $_POST['pushrocket_website_code'] ) ) : '';
			$pushrocket_username     = isset( $_POST['pushrocket_username'] ) ? sanitize_text_field( wp_unslash( $_POST['pushrocket_username'] ) ) : '';
			$pushrocket_password     = isset( $_POST['pushrocket_password'] ) ? sanitize_text_field( wp_unslash( $_POST['pushrocket_password'] ) ) : '';
			$data_to_send            = array(
				'WebsiteURL'  => $pushrocket_panel_url,
				'WebsiteCode' => $pushrocket_website_code,
				'UserName'    => $pushrocket_username,
				'Password'    => $pushrocket_password,
			// Add other fields here.
			);
			$pushrocket_api_url = get_option( 'pushrocket_api_url' );
			// Perform wp_remote_post with all form values.
			$response = wp_remote_post(
				$pushrocket_api_url . '/api/User/GetWebsiteList',
				array(
					'body' => $data_to_send, // Pass the form data here.
				)
			);
			if ( is_array( $response ) ) {
				$response_body    = wp_remote_retrieve_body( $response ); // Response body.
				$decoded_response = json_decode( $response_body, true );
				if ( $decoded_response ) {
					// Now you can work with the JSON data as an associative array.
					$result_status  = $decoded_response['Status'];
					$result_message = $decoded_response['Message'];
					if ( ! $result_status ) {
						set_transient( 'pushrocket_error', $result_message, 30 );
						set_transient( 'pushrocket_show_multiple_site', 'no' );
						wp_safe_redirect( admin_url( 'admin.php?page=pushrocket' ) );
						exit();
					} else {
						set_transient( 'pushrocket_show_multiple_site', 'yes' );
					}
				} else {
					return $value;
				}
			}
			// Return the value to be saved in the database.
			return $value;
		}
		/**
		 * Add custom row action to the post list in the admin panel.
		 *
		 * This function is hooked into the `post_row_actions` filter to add a custom action link
		 * to the row actions of individual posts or web stories in the WordPress admin panel.
		 *
		 * The custom action is 'Send Notification', and it is added only for posts and web stories.
		 * Clicking on this action triggers a JavaScript function to send a push notification associated
		 * with the specific post or web story.
		 *
		 * @param array   $actions An array of row action links.
		 * @param WP_Post $post    The current post object.
		 *
		 * @return array Modified array of row action links.
		 */
		public function add_post_row_actions( $actions, $post ) {
			if ( 'post' === $post->post_type || 'web-story' === $post->post_type ) {
				$actions['send_notification'] =
				'<a href="#" class="push_rocket_send_notification" data-post-id="' . $post->ID . '">Send Notification</a>';
			}
			return $actions;
		}
		/**
		 * Register the JavaScript for the admin area.
		 *
		 * @since    1.0.0
		 */
		public function enqueue_scripts() {
			wp_enqueue_script(
				'push-notifications-by-pushrocket-admin',
				plugin_dir_url( __FILE__ ) . 'js/push-notifications-by-pushrocket-admin.js',
				array( 'jquery' ),
				1.8,
				false
			);
			// Localize the script with the nonce.
			wp_localize_script(
				'push-notifications-by-pushrocket-admin',
				'pushrocket_vars', // The JavaScript object name.
				array(
					'nonce' => wp_create_nonce( 'pushrocket_nonce' ), // Create and pass the nonce.
				)
			);
		}
	}

}
new Notification_Settings();

