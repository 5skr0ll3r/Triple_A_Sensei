<?php
if( !defined('ABSPATH')){
	exit;
}

if(!class_exists('My_Sensei_Stuff')){
	class My_Sensei_Stuff{
		public static function my_sensei_add_popup() {
			if ( is_singular( array( 'lesson', 'course' ) ) ) {
				if ( is_user_logged_in() || self::my_is_sensei_guest() ) {
					add_filter( 'sensei_is_login_required', '__return_false' );
					return;
				}
				
				if ( ! is_user_logged_in() && ! self::my_is_sensei_guest() ) {
					require TRIPLE_A_SENSEI_PATH . 'templates/popup.php';
					add_action( 'wp_footer', 'render_popup' );
				}
			}
		}

		public static function my_sensei_handle_user_choice_redirect() {
			if ( ! is_user_logged_in() && isset( $_REQUEST['choice'] ) ) {
				$choice = sanitize_text_field( $_REQUEST['choice'] );
				$lang = isset($_COOKIE['pll_language']) ? sanitize_text_field($_COOKIE['pll_language']) : '';
				$ref = wp_get_referer();
				if ( ! $ref ) {
					$ref = home_url( add_query_arg( [], $_SERVER['REQUEST_URI'] ) );
				}
				$clean_url = remove_query_arg( [ 'choice', 'token' ], $ref );
				$lr_path = apply_filters('triple_sensei_check_lang_lr', 10);
				switch ( $choice ) {
					case 'guest':
						if(!isset( $_REQUEST['token'])){
							wp_safe_redirect( esc_url_raw( $clean_url ) );
							exit;
						}
						$token = sanitize_text_field($_REQUEST['token']);
						include_once TRIPLE_A_SENSEI_PATH . 'includes/captcha.php';
						if(!verifyGuestCaptcha($token)){
							wp_safe_redirect( esc_url_raw( $clean_url ) );
							exit;
						}

						$new_tmp_user_id = Sensei_Guest_User::create_guest_user();
						if ( $new_tmp_user_id ) {

							$user = new WP_User( $new_tmp_user_id );
							if ( ! $user->has_cap( 'guest_student' ) ) {
								$user->add_cap( 'guest_student' );
							}

							wp_set_current_user( $new_tmp_user_id );
							wp_set_auth_cookie( $new_tmp_user_id, true );

							$ipv4_v6_address = self::get_the_user_ip();
							if(filter_var($ipv4_v6_address, FILTER_VALIDATE_IP)){
								$response = file_get_contents("http://ip-api.com/json/{$ipv4_v6_address}");
								$data = json_decode($response, true);
								if ($data && $data['status'] === 'success') {
									$country = $data['country'];
								} else{
									$country = '';
								}
							} else{
								$ipv4_v6_address = '0.0.0.0';
								$country = '';
							}
							include_once TRIPLE_A_SENSEI_PATH . 'includes/db-api.php';
							$result = Triple_A_Sensei_DB_API::insert($new_tmp_user_id, $user->user_login, $ipv4_v6_address, $country);
							if(!$result){
								error_log('Could not insert user in the database');
							}

							wp_safe_redirect( esc_url_raw( $clean_url ) );
							exit;
						}
						break;
					case 'login':
						//Redirect after login doesn't work redirect_to is not kept and therefor no redirect back to prev page happens
						//Solution either store redirect in $SESSIONS (not gonna enable sessions tho just for redirecting back to the previous page for two cases)
						//Or add hidden input which will take the redirect_to from "$_GET['redirect_to']" 
						$l_path = $lr_path['l_path'];

						$login_url = add_query_arg( 'redirect_to', $clean_url, home_url( $l_path ) );
						wp_safe_redirect( $login_url );
						exit;
						break;
					case 'register':
						//Redirect after register doesn't work redirect_to is not kept and therefor no redirect back to prev page happens
						//Solution either store redirect in $SESSIONS (not gonna enable sessions tho just for redirecting back to the previous page for two cases)
						//Or add hidden input which will take the redirect_to from "$_GET['redirect_to']" 
						$r_path = $lr_path['r_path'];

						//$path = $lang ? '/' . $lang . '/register' : '/register';
						$register_url = add_query_arg( 'redirect_to', $clean_url, home_url( $l_path ) );
						wp_safe_redirect( $register_url );
						exit;
						break;
				}
			}
			//Propably dont need this, except if front-dev
			if ( ! is_user_logged_in() && isset( $_COOKIE[ LOGGED_IN_COOKIE ] ) ) {
				$user_id = wp_validate_auth_cookie( $_COOKIE[ LOGGED_IN_COOKIE ], 'logged_in' );
				if ( $user_id && $user_id > 0 ) {
					$user = get_user_by( 'id', $user_id );
					if ( $user && $user->has_cap( 'guest_student' ) ) {
						wp_set_current_user( $user_id );
					}
				}
			}
		}

		private static function my_is_sensei_guest() {
			$current_user = wp_get_current_user();

			if ( $current_user && $current_user->ID ) {
				if ( $current_user->has_cap( 'guest_student' ) ) {
					return true;
				}
			}
			return false;
		}
		private static function get_the_user_ip() {
			if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
			//check ip from share internet
				$ip = $_SERVER['HTTP_CLIENT_IP'];
			} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			//to check ip is pass from proxy
				$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
			} else {
				$ip = $_SERVER['REMOTE_ADDR'];
			}
			return $ip;
		}
	}
}