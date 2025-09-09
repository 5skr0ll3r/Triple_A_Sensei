<?php
if(!defined('ABSPATH')){
	exit;
}

function render_popup(){
	$popup_texts = apply_filters('triple_check_lang', 10);
	require_once TRIPLE_A_SENSEI_PATH . 'includes/db-api.php';
	$site_key = Triple_A_Sensei_DB_API::get_value('cap-site-key');
	?>
	<script src="https://www.google.com/recaptcha/api.js?render=<?= $site_key ?>"></script>
	<div class="sensei-continue-as-popup">
		<div class="triple-a-buttons-container">
			<h3><?=$popup_texts['message']?></h3>
			
			<div class="top-buttons">
				<button class="continue-with-login triple-a-button"><?=$popup_texts['login']?></button>
				<span class="divider">/</span>
				<button class="continue-with-register triple-a-button"><?=$popup_texts['register']?></button>
			</div>

			<div class="or-text">or</div>

			<div class="middle-button">
				<button class="continue-as-guest triple-a-button"><?=$popup_texts['guest']?></button>
			</div>
		</div>
	</div>
	<div class="sensei-popup-overlay"></div>

	<style>
		.sensei-continue-as-popup{
			background-color: white;
			width: 250px;
			padding: 20px;
			position: fixed;
			top: 50%;
			left: 50%;
			transform: translate(-50%, -50%);
			z-index: 9999;
			display: flex;
			flex-direction: column;
			gap: 10px;
			justify-content: center;
			align-items: center;
			box-shadow: 0 0 10px rgba(0,0,0,0.2);
			border-radius: 8px;
		}

		.sensei-popup-overlay{
			position: fixed;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			background-color: rgba(0,0,0,0.5);
			z-index: 9998;
		}

		.triple-a-buttons-container {
			text-align: center;
			display: flex;
			flex-direction: column;
			gap: 10px;
			width: 100%;
			align-items: center;
		}

		.top-buttons {
			display: flex;
			justify-content: center;
			align-items: center;
			gap: 5px;
			width: 100%;
		}

		.top-buttons .divider {
			font-weight: 600;
			color: #1e293b;
		}

		.or-text {
			font-size: 28px;
			font-weight: 600;
			color: #1e293b;
		}

		.middle-button {
			display: flex;
			justify-content: center;
			width: 100%;
		}

		.triple-a-button {
			background-color: #009490;
			color: #fff;
			padding: 10px 15px;
			border: none;
			border-radius: 4px;
			cursor: pointer;
		}
	</style>

	<script>
		const guestBtn = document.querySelector('.continue-as-guest');
		const loginBtn = document.querySelector('.continue-with-login');
		const registerBtn = document.querySelector('.continue-with-register');
	
		function appendQueryParam(params) {
			let newURL = new URL(window.location.href);

			if (Array.isArray(params) && Array.isArray(params[0])) {
				for (let [param, value] of params) {
					newURL.searchParams.set(param, value);
				}
			} else if (Array.isArray(params)) {
				newURL.searchParams.set(params[0], params[1]);
			}

			return newURL.href;
		}
	
		guestBtn.addEventListener('click', async (event) => {
			event.preventDefault();
			let token;
			var updatedURL
			try{
				token = await captcha(event);
				updatedURL = appendQueryParam([["choice", "guest"],["token", token]]);
				window.location.href = updatedURL;
				
			}catch(e){
				console.log(e);
				window.location.href = window.location.href;
			}
		});
	
		loginBtn.addEventListener('click', (event) => {
			event.preventDefault();
			const updatedURL = appendQueryParam(["choice", "login"]);
			window.location.href = updatedURL;
		});		
	
		registerBtn.addEventListener('click', (event) => {
			event.preventDefault();
			const updatedURL = appendQueryParam(["choice", "register"]);
			window.location.href = updatedURL;
		});
	
		document.body.style.overflow = 'hidden';
	
		function captcha(e) {
			return new Promise((resolve,reject)=>{
				try{
					grecaptcha.ready(function() {
						grecaptcha.execute('<?= $site_key ?>', {action: 'submit'}).then(function(token) {
							return resolve(token);
						});
					});
				} catch(e){
					reject("Something went wrong, please try again\n" + e);
				}
			});
		}
	</script>
	<?php
}
