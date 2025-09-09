	<?php

	require_once WP_PLUGIN_DIR . "/sensei-lms/includes/class-sensei-guest-user.php";


	function render_popup(){ ?>

		<div class="sensei-continue-as-popup" style="
			background-color:white;
			width:250px;
			height:250px;
			position:fixed;
			top:50%;
			left:50%;
			transform:translate(-50%, -50%);
			z-index:9999;
			display:flex;
			justify-content:center;
			align-items:center;
			box-shadow:0 0 10px rgba(0,0,0,0.2);
			border-radius:8px;
		">
			<button class="continue-as-guest">Guest</button>
			<button class="continue-with-login">Login</button>
			<button class="continue-with-register">Register</button>
		</div> 
		<script>
			const guest = document.querySelector('.continue-as-guest');
			const login = document.querySelector('.continue-with-login');
			const register = document.querySelector('.continue-with-register');

			function appendQueryParam(param, value) {
				let newURL = new URL(window.location.href);
				newURL.searchParams.set(param, value);
				return newURL.href;
			}

			guest.addEventListener('click', (event)=>{
				event.preventDefault();
				const updatedURL = appendQueryParam("choice", "guest");
				window.location.href = updatedURL;
				});

			login.addEventListener('click', (event)=>{
				event.preventDefault();
				const updatedURL = appendQueryParam("choice", "login");
				window.location.href = updatedURL;
				});
							
			register.addEventListener('click', (event)=>{
				event.preventDefault();
				const updatedURL = appendQueryParam("choice", "register");
				window.location.href = updatedURL;
				});
		</script>

	<?php
	}

	add_action('template_redirect', 'is_cource_or_lesson', 10);
	function is_cource_or_lesson(){
		if( is_singular(array('lesson', 'course'))){
			add_filter( 'sensei_is_login_required', 'continue_with_guest_register_or_login', 10, 2 );
			if( !is_user_logged_in() ){
				add_action('wp_footer', 'render_popup');
			}
		}
		return;
	}

	function remove_popup_render(){
		if( has_action('wp_footer', 'render_popup') ){
			remove_action('wp_footer', 'render_popup');
		}
	}

	//add_filter( 'sensei_is_login_required', 'continue_with_guest_register_or_login', 10, 2 );
	function continue_with_guest_register_or_login($must_be_logged_to_view_lesson, $course_id){
		remove_popup_render();

		if( is_user_logged_in() ){
			error_log(print_r(wp_get_current_user(), true));
			return false;
		}

		if( isset( $_REQUEST['choice'])){
			$current_page = get_permalink();
			$choice = sanitize_text_field($_REQUEST['choice']);
			$path = '';
			switch ($choice) {
				case 'guest':
					$new_tmp_user_id = Sensei_Guest_User::create_guest_user();
					wp_set_current_user( $new_tmp_user_id );
					wp_set_auth_cookie( $new_tmp_user_id, true );
					$path = '';
					break;
				case 'login':
					$path = '/login';
					break;
				case 'register':
					$path = '/register';
					break;
			}

			if($path){
				wp_safe_redirect( 
					add_query_arg(
						array(
							'redirect_to' => $current_page
						), 
						home_url( $path ) ) );
				exit;
			}
		}
	}

	add_filter('registration_redirect', 'sensei_universal_redirect');
	add_filter('login_redirect', 'sensei_universal_redirect', 10, 3);
	function sensei_universal_redirect( $redirect_to, $requested_redirect_to = null, $user = null ) {
		if ( isset( $_REQUEST['redirect_to'] ) ) {
			return esc_url_raw( $_REQUEST['redirect_to'] );
		}
		return home_url();
	}