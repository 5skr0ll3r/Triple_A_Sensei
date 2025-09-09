<?php

if(!defined('ABSPATH')){
	exit;
}

include_once TRIPLE_A_SENSEI_PATH . 'includes/my-sensei-stuff.php';

if(!has_action( 'wp_loaded', array('My_Sensei_Stuff', 'my_sensei_handle_user_choice_redirect' ))){
	add_action( 'wp_loaded', array('My_Sensei_Stuff', 'my_sensei_handle_user_choice_redirect' ), 1 );
}

if(!has_action( 'template_redirect', array('My_Sensei_Stuff', 'my_sensei_add_popup' ))){
	add_action( 'template_redirect', array('My_Sensei_Stuff', 'my_sensei_add_popup' ), 10 );
}

include_once TRIPLE_A_SENSEI_PATH . 'includes/export.php';

if(!has_action('admin_post_triple_a_export', 'triple_a_export_handler')){
	add_action('admin_post_triple_a_export', 'triple_a_export_handler');
}