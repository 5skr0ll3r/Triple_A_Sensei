<?php
/*
Plugin Name: Triple A Sensei
Plugin URI: https://yourdomain.com
Description: Enable login/register and guest access to SenseiLMS.
Version: 1.0
Author: Charalambos Rentoumis
*/
//Triple A Sensei:
//Allow Access to All


if( ! defined('ABSPATH') ){
	exit;
}

if( !defined('TRIPLE_A_SENSEI_PATH')){
	define('TRIPLE_A_SENSEI_PATH', plugin_dir_path(__FILE__) );
}

add_action('plugins_loaded', function() {
	include_once TRIPLE_A_SENSEI_PATH . 'includes/db-api.php';
	Triple_A_Sensei_DB_API::init();
});

function init_tripple_a_plugin(){
	if(class_exists('Triple_A_Sensei_Plugin')){
		$pl = new Triple_A_Sensei_Plugin();
	}
}

if( !class_exists('Triple_A_Sensei_Plugin')){

	class Triple_A_Sensei_Plugin{
		public function __construct(){
			if(!has_action( 'admin_menu', array( $this, 'add_menu_item' ))){
				add_action( 'admin_menu', array( $this, 'add_menu_item' ));
			}

			include_once TRIPLE_A_SENSEI_PATH . 'hooks/actions.php';
			include_once TRIPLE_A_SENSEI_PATH . 'hooks/filters.php';

		}

		public static function activate(){
			include_once TRIPLE_A_SENSEI_PATH . 'includes/activate.php';
			Triple_A_Sensei_Activate::activate();
		}

		public static function deactivate(){
			include_once TRIPLE_A_SENSEI_PATH . 'includes/deactivate.php';
			Triple_A_Sensei_Deactivate::deactivate();
		}

		public function add_menu_item(){
			add_menu_page( 'Triple_A_Sensei_Settings', 'Triple_A_Sensei', 'manage_options', 'tas_slug', array( $this, 'render_settings' ) );
		}

		public function render_settings(){
			include TRIPLE_A_SENSEI_PATH . 'templates/admin/admin_panel.php';
		}
	}
}

register_activation_hook( __FILE__, array( 'Triple_A_Sensei_Plugin', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'Triple_A_Sensei_Plugin', 'deactivate' ) );

if(!has_action('init', 'init_tripple_a_plugin')){
	add_action('init', 'init_tripple_a_plugin');
}

