<?php
/*
Plugin Name: Login Redirect
Plugin URI: 
Description:
Author: Andrew Billits
Version: 1.0.1
Author URI:
*/

/* 
Copyright 2007-2009 Incsub (http://incsub.com)

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

//------------------------------------------------------------------------//
//---Config---------------------------------------------------------------//
//------------------------------------------------------------------------//

$login_redirect_url = 'http://domain.tld'; // the url you want users to be redirected too after logging in

//------------------------------------------------------------------------//
//---Hook-----------------------------------------------------------------//
//------------------------------------------------------------------------//
add_action('login_head', 'login_redirect_to', 1);
add_filter('wp_redirect', 'login_redirect');
//------------------------------------------------------------------------//
//---Functions------------------------------------------------------------//
//------------------------------------------------------------------------//

function login_redirect($location){
	global $login_redirect_url;
	if ( $location == 'login-redirect' ) {
		$location = $login_redirect_url;
	}
	return $location;
}

function login_redirect_to(){
	if ( empty( $_GET['redirect_to'] ) && empty( $_GET['action'] ) && empty( $_GET['username'] ) && empty( $_GET['checkemail'] ) ) {
		if ( empty( $_POST['pwd'] ) && empty( $_POST['log'] ) ) {
			echo "
			<SCRIPT LANGUAGE='JavaScript'>
			window.location='wp-login.php?redirect_to=login-redirect';
			</script>
			";
		}
	}
}

?>