<?php
/*
Plugin Name: Login Redirect
Plugin URI: http://premium.wpmudev.org/project/login-redirect
Description: Redirects users to specified url after they've logged in, replacing the default 'go to dashboard' behavior.
Author: WPMUDEV
Version: 1.0.8
Text Domain: login_redirect
Author URI: http://premium.wpmudev.org/
WDP ID: 43
*/

/*
Copyright 2007-2013 Incsub (http://incsub.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License (Version 2 - GPLv2) as published by
the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

/**
 * Plugin main class
 **/
class Login_Redirect {

	function __construct() {
		include_once( 'login-redirect-files/wpmudev-dash-notification.php' );

		if(!isset($_REQUEST['redirect_to']) || $_REQUEST['redirect_to'] == admin_url() )
			add_filter( 'login_redirect', array( &$this, 'redirect' ), 999, 3 );

		if($this->is_plugin_active_for_network( plugin_basename( __FILE__ ) )) {
			add_action( 'wpmu_options', array( &$this, 'network_option' ) );
			add_action( 'update_wpmu_options', array( &$this, 'update_network_option' ) );
		}
		add_action( 'admin_init', array( &$this, 'add_settings_field' ) );

		add_action( 'plugins_loaded', array( &$this, 'load_translation' ) );
	}

	function load_translation() {
		// load text domain
		if ( defined( 'WPMU_PLUGIN_DIR' ) && file_exists( WPMU_PLUGIN_DIR . '/login-redirect.php' ) ) {
			load_muplugin_textdomain( 'login_redirect', 'login-redirect-files/languages' );
		} else {
			load_plugin_textdomain( 'login_redirect', false, dirname( plugin_basename( __FILE__ ) ) . '/login-redirect-files/languages' );
		}
	}

	/**
	 * Redirect user on login
	 **/
	function redirect( $redirect_to, $requested_redirect_to, $user ) {
		$interim_login = isset( $_REQUEST['interim-login'] );
		$reauth = empty( $_REQUEST['reauth'] ) ? false : true;

		if( $this->is_plugin_active_for_network( plugin_basename( __FILE__ ) ) )
			$login_redirect_url = get_site_option( 'login_redirect_url' );
		else
			$login_redirect_url = get_option( 'login_redirect_url' );

		if ( !is_wp_error( $user ) && !$reauth && !$interim_login && !empty( $login_redirect_url ) ) {
			wp_redirect( $login_redirect_url );
			exit();
		}

		return $redirect_to;
	}

	/**
	 * Network option
	 **/
	function network_option() {
		?>
		<h3><?php _e( 'Login Redirect', 'login_redirect' ); ?></h3>
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><label for="login_redirect_url"><?php _e( 'Redirect to', 'login_redirect' ) ?></label></th>
				<td>
					<input name="login_redirect_url" type="text" id="login_redirect_url" value="<?php echo esc_attr( get_site_option( 'login_redirect_url' ) ) ?>" size="40" />
					<br />
					<?php _e( 'The URL users will be redirected to after login.', 'login_redirect' ) ?>
				</td>
			</tr>
		</table>
		<?php
	}

	/**
	 * Save option in the option
	 **/
	function update_network_option() {
		update_site_option( 'login_redirect_url', stripslashes( $_POST['login_redirect_url'] ) );
	}

	/**
	 * Add setting field for singlesite
	 **/
	function add_settings_field() {
		if( $this->is_plugin_active_for_network( plugin_basename( __FILE__ ) ) )
			return;

		add_settings_section( 'login_redirect_setting_section', __( 'Login Redirect', 'login_redirect' ), '__return_false', 'general' );

		add_settings_field( 'login_redirect_url', __( 'Redirect to', 'login_redirect' ), array( &$this, 'site_option' ), 'general', 'login_redirect_setting_section' );

		register_setting( 'general', 'login_redirect_url' );
	}

	/**
	 * Setting field for singlesite
	 **/
	function site_option() {
		echo '<input name="login_redirect_url" type="text" id="login_redirect_url" value="' . esc_attr( get_option( 'login_redirect_url' ) ) . '" size="40" />';
	}

	/**
	 * Verify if plugin is network activated
	 **/
	function is_plugin_active_for_network( $plugin ) {
		if ( !is_multisite() )
			return false;

		$plugins = get_site_option( 'active_sitewide_plugins');
		if ( isset($plugins[$plugin]) )
			return true;

		return false;
	}

}

$login_redirect = new Login_Redirect();
