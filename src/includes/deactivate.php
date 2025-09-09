<?php

if( !defined('ABSPATH') ){
	exit;
}


if( !class_exists('Triple_A_Sensei_Deactivate')){
	class Triple_A_Sensei_Deactivate{
		public static function deactivate(){
			flush_rewrite_rules();
		}
	}	
}
