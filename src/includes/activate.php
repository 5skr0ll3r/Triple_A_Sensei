<?php

if( !defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Plugin Activation/Initiation of Database Tables.
 */

if( !class_exists('Triple_A_Sensei_Activate')){
	class Triple_A_Sensei_Activate{
		public static function activate(){
			include_once TRIPLE_A_SENSEI_PATH . 'includes/db-api.php';
			Triple_A_Sensei_DB_API::init();
			Triple_A_Sensei_DB_API::create_table();
			flush_rewrite_rules();
		}
	}	
}
