<?php

if(!defined('ABSPATH')){
	exit;
}

add_filter('triple_sensei_check_lang', 'triple_check_lang');
function triple_check_lang(){
	$gr_popup_texts = array(
		'message' => 'Επιλέξτε μέθοδο πρόσβασης:',
		'guest' => 'Ως επισκέπτης',
		'login' => 'Σύνδεση',
		'register' => 'Εγγραφή'
	);
	$en_popup_texts = array(
		'message' => 'Choose your access method:',
		'guest' => 'As a guest',
		'login' => 'Login',
		'register' => 'Register'
	);
	$sv_popup_texts = array(
		'message' => 'Välj din åtkomstmetod:',
		'guest' => 'Som gäst',
		'login' => 'Logga in',
		'register' => 'Registrera'
	);
	$fi_popup_texts = array(
		'message' => 'Valitse käyttötapasi:',
		'guest' => 'Vieraana',
		'login' => 'Kirjaudu sisään',
		'register' => 'Rekisteröidy'
	);
	$it_popup_texts = array(
		'message' => 'Scegli il tuo metodo di accesso:',
		'guest' => 'Come ospite',
		'login' => 'Accedi',
		'register' => 'Registrati'
	);
	
	$lang = isset($_COOKIE['pll_language']) ? $_COOKIE['pll_language'] : '';
	return match ($lang) {
		'el' => $gr_popup_texts,
		'sv' => $sv_popup_texts,
		'fi' => $fi_popup_texts,
		'it' => $it_popup_texts,
		default => $en_popup_texts,
	};
}



add_filter('triple_sensei_check_lang_lr', 'triple_a_check_lang_login_register');
function triple_a_check_lang_login_register(){
	$lang = isset($_COOKIE['pll_language']) ? $_COOKIE['pll_language'] : '';

	$l_path = match ($lang) {
		'el' => '/el/login-greek',
		'sv' => '/sv/login-svenska',
		'fi' => '/fi/login-suomi',
		'it' => '/it/login-italiano',
		default => '/login',
	};

	$r_path = match ($lang) {
		'el' => '/el/register-greek',
		'sv' => '/sv/register-svenska',
		'fi' => '/fi/register-suomi',
		'it' => '/it/register-italiano',
		default => '/register',
	};

	return array( 'l_path' => $l_path, 'r_path' => $r_path );
}