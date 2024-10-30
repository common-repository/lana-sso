<?php
/**
 * Plugin Name: Lana Single Sign On
 * Plugin URI: https://lana.codes/product/lana-sso/
 * Description: Creates the ability to login using Single Sign On via Lana Passport.
 * Version: 1.2.0
 * Author: Lana Codes
 * Author URI: https://lana.codes/
 * Text Domain: lana-sso
 * Domain Path: /languages
 */

defined( 'ABSPATH' ) or die();
define( 'LANA_SSO_VERSION', '1.2.0' );
define( 'LANA_SSO_DIR_URL', plugin_dir_url( __FILE__ ) );
define( 'LANA_SSO_DIR_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Language
 * load
 */
load_plugin_textdomain( 'lana-sso', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

/**
 * Login styles
 */
function lana_sso_login_styles() {
	wp_register_style( 'lana-sso-login', LANA_SSO_DIR_URL . '/assets/css/lana-sso-login.css', array(), LANA_SSO_VERSION );
	wp_enqueue_style( 'lana-sso-login' );
}

add_action( 'login_enqueue_scripts', 'lana_sso_login_styles' );

/**
 * Styles
 * load in admin
 */
function lana_sso_admin_styles() {

	wp_register_style( 'toastr', LANA_SSO_DIR_URL . '/assets/libs/toastr/css/toastr.min.css', array(), '2.1.1' );
	wp_enqueue_style( 'toastr' );

	wp_register_style( 'lana-sso', LANA_SSO_DIR_URL . '/assets/css/lana-sso-admin.css', array(), LANA_SSO_VERSION );
	wp_enqueue_style( 'lana-sso' );
}

add_action( 'admin_enqueue_scripts', 'lana_sso_admin_styles' );

/**
 * JavaScript
 * load in admin
 */
function lana_sso_admin_scripts() {

	/** toastr js */
	wp_register_script( 'toastr', LANA_SSO_DIR_URL . '/assets/libs/toastr/js/toastr.min.js', array( 'jquery' ), '2.1.1' );
	wp_enqueue_script( 'toastr' );

	/** lana sso admin js */
	wp_register_script( 'lana-sso-admin', LANA_SSO_DIR_URL . '/assets/js/lana-sso-admin.js', array(
		'jquery',
		'toastr',
	), LANA_SSO_VERSION, true );
	wp_enqueue_script( 'lana-sso-admin' );

	/** add l10n to lana sso admin js */
	wp_localize_script( 'lana-sso-admin', 'lana_sso_l10n', array(
		'copied_to_clipboard'               => esc_html__( 'The text was copied to your clipboard!', 'lana-sso' ),
		'browser_not_support_clipboard_api' => esc_html__( 'Opps! Your browser does not support the Clipboard API!', 'lana-sso' ),
	) );
}

add_action( 'admin_enqueue_scripts', 'lana_sso_admin_scripts' );

/**
 * Add plugin action links
 *
 * @param $links
 *
 * @return mixed
 */
function lana_sso_add_plugin_action_links( $links ) {

	$settings_url = esc_url( admin_url( 'options-general.php?page=lana-sso-settings.php' ) );

	/** add settings link */
	$settings_link = sprintf( '<a href="%s">%s</a>', $settings_url, esc_html__( 'Settings', 'lana-sso' ) );
	array_unshift( $links, $settings_link );

	return $links;
}

add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'lana_sso_add_plugin_action_links' );

/**
 * Lana SSO
 * add admin page
 */
function lana_sso_admin_menu() {
	add_options_page( esc_html__( 'Lana SSO Settings', 'lana-sso' ), esc_html__( 'Lana SSO', 'lana-sso' ), 'manage_options', 'lana-sso-settings.php', 'lana_sso_settings_page' );

	/** call register settings function */
	add_action( 'admin_init', 'lana_sso_register_settings' );
}

add_action( 'admin_menu', 'lana_sso_admin_menu' );

/**
 * Register settings
 */
function lana_sso_register_settings() {

	if ( ! defined( 'LANA_SSO_CLIENT_ID' ) ) {
		register_setting( 'lana-sso-settings-group', 'lana_sso_client_id' );
	}

	if ( ! defined( 'LANA_SSO_CLIENT_SECRET' ) ) {
		register_setting( 'lana-sso-settings-group', 'lana_sso_client_secret' );
	}

	register_setting( 'lana-sso-settings-group', 'lana_sso_authorize_url' );
	register_setting( 'lana-sso-settings-group', 'lana_sso_token_url' );
	register_setting( 'lana-sso-settings-group', 'lana_sso_resource_url' );
}

/**
 * Lana SSO Settings page
 */
function lana_sso_settings_page() {
	?>
    <div class="wrap">
        <h2><?php esc_html_e( 'Lana SSO Settings', 'lana-sso' ); ?></h2>

        <hr/>
        <a href="<?php echo esc_url( 'https://lana.codes/' ); ?>" target="_blank">
            <img src="<?php echo esc_url( LANA_SSO_DIR_URL . '/assets/img/plugin-header.png' ); ?>"
                 alt="<?php esc_attr_e( 'Lana Codes', 'lana-sso' ); ?>"/>
        </a>
        <hr/>

        <table class="form-table">
            <tr>
                <th scope="row">
                    <label>
						<?php esc_html_e( 'SSO URI', 'lana-sso' ); ?>
                    </label>
                    <span class="dashicons dashicons-info-outline"
                          title="<?php esc_attr_e( 'This endpoint handles the OAuth request with authorization code grant type.', 'lana-sso' ); ?>"></span>
                </th>
                <td>
                    <span id="sso-uri"><?php echo esc_url( home_url( '?auth=sso' ) ); ?></span>

                    <button class="button button-with-icon copy-to-clipboard hide-if-no-js" data-target="#sso-uri">
                        <span class="dashicons dashicons-clipboard"></span>
						<?php esc_html_e( 'Copy to Clipboard', 'lana-sso' ); ?>
                    </button>
                </td>
            </tr>
        </table>
        <hr/>

        <form method="post" action="<?php echo esc_url( admin_url( 'options.php' ) ); ?>">
			<?php settings_fields( 'lana-sso-settings-group' ); ?>

            <h2 class="title"><?php esc_html_e( 'Client Settings', 'lana-sso' ); ?></h2>
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="lana-sso-client-id">
							<?php esc_html_e( 'Client ID', 'lana-sso' ); ?>
                        </label>
                    </th>
                    <td>
						<?php if ( defined( 'LANA_SSO_CLIENT_ID' ) ): ?>
                            <span id="constant-client-id" class="regular-text">
								<?php echo wp_kses( sprintf( __( 'statically set with %s constant value', 'lana-sso' ), '<code>LANA_SSO_CLIENT_ID</code>' ), array( 'code' => array() ) ); ?>
							</span>

							<?php if ( get_option( 'lana_sso_client_id', false ) ): ?>
                                <span class="button-separator"></span>
                                <a href="<?php echo esc_url( wp_nonce_url( add_query_arg( array(
									'action' => 'lana_sso_delete_client_id_from_wpdb',
								), 'admin-post.php' ), 'lana_sso_delete_client_id_from_wpdb' ) ); ?>"
                                   class="button button-with-icon" name="lana_sso_client_id">
                                    <span class="dashicons dashicons-database-remove"></span>
									<?php esc_html_e( 'Delete previous value from database', 'lana-sso' ); ?>
                                </a>
							<?php endif; ?>

						<?php else: ?>
                            <input type="text" name="lana_sso_client_id" id="lana-sso-client-id" class="regular-text"
                                   value="<?php echo esc_attr( get_option( 'lana_sso_client_id' ) ); ?>">
						<?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="lana-sso-client-secret">
							<?php esc_html_e( 'Client Secret', 'lana-sso' ); ?>
                        </label>
                    </th>
                    <td>
						<?php if ( defined( 'LANA_SSO_CLIENT_SECRET' ) ): ?>
                            <span id="constant-client-secret" class="regular-text">
								<?php echo wp_kses( sprintf( __( 'statically set with %s constant value', 'lana-sso' ), '<code>LANA_SSO_CLIENT_SECRET</code>' ), array( 'code' => array() ) ); ?>
							</span>

							<?php if ( get_option( 'lana_sso_client_secret', false ) ): ?>
                                <span class="button-separator"></span>
                                <a href="<?php echo esc_url( wp_nonce_url( add_query_arg( array(
									'action' => 'lana_sso_delete_client_secret_from_wpdb',
								), 'admin-post.php' ), 'lana_sso_delete_client_secret_from_wpdb' ) ); ?>"
                                   class="button button-with-icon">
                                    <span class="dashicons dashicons-database-remove"></span>
									<?php esc_html_e( 'Delete previous value from database', 'lana-sso' ); ?>
                                </a>
							<?php endif; ?>

						<?php else: ?>
                            <input type="text" name="lana_sso_client_secret" id="lana-sso-client-secret"
                                   class="regular-text"
                                   value="<?php echo esc_attr( get_option( 'lana_sso_client_secret' ) ); ?>">
						<?php endif; ?>
                    </td>
                </tr>
            </table>

            <h2 class="title"><?php esc_html_e( 'Endpoint Settings', 'lana-sso' ); ?></h2>
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="lana-sso-authorize-url">
							<?php esc_html_e( 'Authorize URL', 'lana-sso' ); ?>
                        </label>
                    </th>
                    <td>
                        <input type="url" name="lana_sso_authorize_url" id="lana-sso-authorize-url" class="regular-text"
                               value="<?php echo esc_attr( get_option( 'lana_sso_authorize_url' ) ); ?>">
                        <p class="description">
							<?php esc_html_e( 'This is used to get the authorization code.', 'lana-sso' ); ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="lana-sso-token-url">
							<?php esc_html_e( 'Access Token URL', 'lana-sso' ); ?>
                        </label>
                    </th>
                    <td>
                        <input type="url" name="lana_sso_token_url" id="lana-sso-token-url" class="regular-text"
                               value="<?php echo esc_attr( get_option( 'lana_sso_token_url' ) ); ?>">
                        <p class="description">
							<?php esc_html_e( 'This is used to exchange the authorization code for an access token.', 'lana-sso' ); ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="lana-sso-resource-url">
							<?php esc_html_e( 'Resource URL', 'lana-sso' ); ?>
                        </label>
                    </th>
                    <td>
                        <input type="url" name="lana_sso_resource_url" id="lana-sso-resource-url" class="regular-text"
                               value="<?php echo esc_attr( get_option( 'lana_sso_resource_url' ) ); ?>">
                        <p class="description">
							<?php esc_html_e( 'This is used to get the user information.', 'lana-sso' ); ?>
                        </p>
                    </td>
                </tr>
            </table>

            <p class="submit">
                <input type="submit" class="button-primary" value="<?php esc_attr_e( 'Save Changes', 'lana-sso' ); ?>"/>
            </p>

        </form>
    </div>
	<?php
}

/**
 * Lana SSO
 * delete client id from database
 */
function lana_sso_delete_client_id_from_wpdb() {

	check_admin_referer( 'lana_sso_delete_client_id_from_wpdb' );

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'Sorry, you are not allowed to delete client id from database.', 'lana-sso' ) );
	}

	delete_option( 'lana_sso_client_id' );

	/** redirect */
	wp_safe_redirect( add_query_arg( 'updated', 'true', wp_get_referer() ) );
	exit;
}

add_action( 'admin_post_lana_sso_delete_client_id_from_wpdb', 'lana_sso_delete_client_id_from_wpdb' );

/**
 * Lana SSO
 * delete client secret from database
 */
function lana_sso_delete_client_secret_from_wpdb() {

	check_admin_referer( 'lana_sso_delete_client_secret_from_wpdb' );

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'Sorry, you are not allowed to delete client secret from database.', 'lana-sso' ) );
	}

	delete_option( 'lana_sso_client_secret' );

	/** redirect */
	wp_safe_redirect( add_query_arg( 'updated', 'true', wp_get_referer() ) );
	exit;
}

add_action( 'admin_post_lana_sso_delete_client_secret_from_wpdb', 'lana_sso_delete_client_secret_from_wpdb' );

/**
 * Lana SSO
 * login form sso button
 */
function lana_sso_login_form_sso_button() {
	?>
    <div class="lana-sso">
        <div class="or-separator">
            <span class="or-text"><?php esc_html_e( 'or', 'lana-sso' ); ?></span>
        </div>
        <a href="<?php echo esc_url( home_url( '?auth=sso' ) ); ?>"
           class="button button-primary button-large button-sso">
			<?php esc_html_e( 'Single Sign On', 'lana-sso' ); ?>
        </a>
        <div class="clearfix"></div>
    </div>
	<?php
}

add_action( 'login_form', 'lana_sso_login_form_sso_button', 20 );

/**
 * Lana SSO
 * add rewrite rules
 *
 * @param $rules
 *
 * @return string[]
 */
function lana_sso_add_rewrite_rules( $rules ) {
	global $wp_rewrite;
	$new_rule = array( 'auth/(.+)' => 'index.php?auth=' . $wp_rewrite->preg_index( 1 ) );

	return $new_rule + $rules;
}

add_filter( 'rewrite_rules_array', 'lana_sso_add_rewrite_rules' );

/**
 * Lana SSO
 * add query vars
 */
function lana_sso_add_query_vars( $query_vars ) {
	$query_vars[] = 'auth';

	return $query_vars;
}

add_filter( 'query_vars', 'lana_sso_add_query_vars' );

/**
 * Lana SSO
 * request
 */
function lana_sso_request( $wp ) {

	/** check auth in query */
	if ( ! isset( $wp->query_vars['auth'] ) ) {
		return;
	}

	/** check is sso auth */
	if ( 'sso' !== $wp->query_vars['auth'] ) {
		return;
	}

	/** redirect to home if user logged in */
	if ( is_user_logged_in() ) {
		wp_redirect( home_url() );
		exit;
	}

	/** handle error */
	if ( isset( $_REQUEST['error'] ) ) {

		/** use error code */
		$error = sanitize_text_field( wp_unslash( $_REQUEST['error'] ) );

		/** set error description from the error code */
		$error_description = apply_filters( 'lana_sso_request_error_description_from_error_code', $error );

		/** check error description */
		if ( isset( $_REQUEST['error_description'] ) ) {
			$error_description = sanitize_text_field( wp_unslash( $_REQUEST['error_description'] ) );
		}

		/** filter error description */
		$error_description = apply_filters( 'lana_sso_request_error_description', $error_description );

		wp_die( esc_html( $error_description ), '', array(
			'link_text' => esc_html__( '&laquo; Back to WordPress Login', 'lana-sso' ),
			'link_url'  => esc_url( wp_login_url() ),
		) );
	}

	/** we don't have an authorization code, so redirect to authorize */
	if ( ! isset( $_GET['code'] ) ) {

		/** redirect to authorization url */
		wp_redirect( lana_sso_get_authorization_url() );
		exit;

	} else {

		try {
			/** check state */
			if ( ! isset( $_GET['state'] ) ) {
				wp_die( esc_html__( 'The state is empty.', 'lana-sso' ) );
			}

			/** verify state */
			if ( ! wp_verify_nonce( sanitize_key( $_GET['state'] ), 'oauth2' ) ) {
				wp_die( esc_html__( 'That state was incorrect.', 'lana-sso' ) );
			}

			/** get an access token using the authorization code grant */
			$oauth2_token = lana_sso_get_access_token( 'authorization_code', array(
				'code' => sanitize_text_field( wp_unslash( $_GET['code'] ) ),
			) );

			/** using the access token get resource */
			$oauth2_resource = lana_sso_get_resource( $oauth2_token );

			/** sso login */
			lana_sso_login( $oauth2_resource );

		} catch ( Exception $e ) {

			wp_die( esc_html( $e->getMessage() ) );
		}
	}
}

add_action( 'parse_request', 'lana_sso_request' );

/**
 * Lana SSO
 * get authorization url
 * @return string
 */
function lana_sso_get_authorization_url() {

	return add_query_arg( array(
		'response_type' => 'code',
		'client_id'     => lana_sso_get_client_id(),
		'redirect_uri'  => home_url( '?auth=sso' ),
		'state'         => wp_create_nonce( 'oauth2' ),
	), get_option( 'lana_sso_authorize_url' ) );
}

/**
 * Lana SSO
 * get access token
 *
 * @param $grant_type
 * @param $options
 *
 * @return mixed
 * @throws Exception
 */
function lana_sso_get_access_token( $grant_type, $options ) {

	$params = array(
		'client_id'     => lana_sso_get_client_id(),
		'client_secret' => lana_sso_get_client_secret(),
		'redirect_uri'  => home_url( '?auth=sso' ),
	);

	if ( 'authorization_code' == $grant_type ) {

		/** get code */
		$code = $options['code'];

		/** set params */
		$params = array_merge( $params, array(
			'grant_type' => 'authorization_code',
			'code'       => $code,
		) );
	}

	/** oauth2 token request in oauth2 server */
	$oauth2_token_response = wp_remote_post( get_option( 'lana_sso_token_url' ), array(
		'body' => $params,
	) );

	/** check response error */
	if ( is_wp_error( $oauth2_token_response ) ) {
		throw new Exception( $oauth2_token_response->get_error_message() );
	}

	/** get oauth2 token from response body */
	$oauth2_token = json_decode( wp_remote_retrieve_body( $oauth2_token_response ), true );

	/** check response body error */
	if ( isset( $oauth2_token['error'] ) ) {
		throw new Exception( $oauth2_token['error_description'] );
	}

	return $oauth2_token;
}

/**
 * Lana SSO
 * get resource
 *
 * @param $oauth2_token
 *
 * @return mixed
 * @throws Exception
 */
function lana_sso_get_resource( $oauth2_token ) {

	/** oauth2 resource request in oauth2 server */
	$oauth2_resource_response = wp_remote_get( add_query_arg( array(
		'access_token' => $oauth2_token['access_token'],
	), get_option( 'lana_sso_resource_url' ) ) );

	/** check response error */
	if ( is_wp_error( $oauth2_resource_response ) ) {
		throw new Exception( $oauth2_resource_response->get_error_message() );
	}

	/** get oauth2 resource from response body */
	$oauth2_resource = json_decode( wp_remote_retrieve_body( $oauth2_resource_response ), true );

	/** filter oauth2 resource */
	$oauth2_resource = apply_filters( 'lana_sso_oauth2_resource', $oauth2_resource );

	return $oauth2_resource;
}

/**
 * Lana SSO
 * login
 *
 * @param $oauth2_resource
 *
 * @throws Exception
 */
function lana_sso_login( $oauth2_resource ) {

	/** get sso login redirect url */
	$lana_sso_login_redirect_url = apply_filters( 'lana_sso_login_redirect_url', get_dashboard_url() );

	/** get user id by username */
	$user_id = username_exists( $oauth2_resource['user_login'] );

	/** get user id by email */
	if ( ! $user_id ) {
		$user_id = email_exists( $oauth2_resource['user_email'] );
	}

	/** filter user id */
	$user_id = apply_filters( 'lana_sso_login_user_id', $user_id, $oauth2_resource );

	/** user id exists - login user */
	if ( $user_id ) {

		/** login exists user action */
		do_action( 'lana_sso_login_exists_user', $user_id, $oauth2_resource );

		/** action before auth */
		do_action( 'lana_sso_login_before_auth', $user_id, $oauth2_resource );

		wp_clear_auth_cookie();
		wp_set_current_user( $user_id );
		wp_set_auth_cookie( $user_id );

		/** action after auth */
		do_action( 'lana_sso_login_after_auth', $user_id, $oauth2_resource );

		if ( is_user_logged_in() ) {
			wp_safe_redirect( $lana_sso_login_redirect_url );
			exit;
		}

		throw new Exception( esc_html__( 'Error: Failed to login with this user using SSO.', 'lana-sso' ) );
	}

	/** user id not exists - register user and login */
	if ( ! $user_id && get_site_option( 'users_can_register' ) ) {

		/** register not exists user action */
		do_action( 'lana_sso_register_not_exists_user', $user_id, $oauth2_resource );

		/**
		 * userdata for insert user
		 * from oauth2 resource
		 */
		$userdata = array(
			'user_login' => $oauth2_resource['user_login'],
			'user_pass'  => wp_generate_password(),
			'user_email' => $oauth2_resource['user_email'],
		);

		/**
		 * roles for insert user
		 * from oauth2 resource
		 */
		$roles = $oauth2_resource['roles'];

		/** set first name */
		if ( isset( $oauth2_resource['first_name'] ) ) {
			$userdata['first_name'] = $oauth2_resource['first_name'];
		}

		/** set last name */
		if ( isset( $oauth2_resource['last_name'] ) ) {
			$userdata['last_name'] = $oauth2_resource['last_name'];
		}

		/** set role */
		if ( isset( $roles[0] ) ) {
			$userdata['role'] = $roles[0];
			unset( $roles );
		}

		$userdata = apply_filters( 'lana_sso_login_register_userdata', $userdata, $oauth2_resource );

		/** create user */
		$user_id = wp_insert_user( $userdata );

		/** check insert user error */
		if ( is_wp_error( $user_id ) ) {
			throw new Exception( $user_id->get_error_message() );
		}

		/** get user */
		$user = new WP_User( $user_id );

		/** add roles */
		if ( ! empty( $roles ) ) {
			foreach ( $roles as $role ) {
				$user->add_role( $role );
			}
		}

		/** action before auth */
		do_action( 'lana_sso_register_before_auth', $user_id, $oauth2_resource );

		wp_clear_auth_cookie();
		wp_set_current_user( $user_id );
		wp_set_auth_cookie( $user_id );

		/** action after auth */
		do_action( 'lana_sso_register_after_auth', $user_id, $oauth2_resource );

		if ( is_user_logged_in() ) {
			wp_safe_redirect( $lana_sso_login_redirect_url );
			exit;
		}

		throw new Exception( esc_html__( 'Error: Failed to log in with this created user using SSO.', 'lana-sso' ) );
	}

	throw new Exception( esc_html__( 'Single Sign On Failed.', 'lana-sso' ) );
}

/**
 * Lana SSO
 * get client id
 *
 * @return mixed
 */
function lana_sso_get_client_id() {
	if ( defined( 'LANA_SSO_CLIENT_ID' ) ) {
		return LANA_SSO_CLIENT_ID;
	}

	return get_option( 'lana_sso_client_id' );
}

/**
 * Lana SSO
 * get client secret
 *
 * @return mixed
 */
function lana_sso_get_client_secret() {
	if ( defined( 'LANA_SSO_CLIENT_SECRET' ) ) {
		return LANA_SSO_CLIENT_SECRET;
	}

	return get_option( 'lana_sso_client_secret' );
}