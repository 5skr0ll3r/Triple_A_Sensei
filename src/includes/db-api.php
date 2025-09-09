<?php

if ( !defined('ABSPATH') ) {
	exit;
}


/**
 * Class Triple_A_Sensei_DB_API.
 *
 * API to interact with the database.
 */


if( !class_exists('Triple_A_Sensei_DB_API') ){
	class Triple_A_Sensei_DB_API{
		public static $plugins_tables_prefix = 'tas_';
		public static $wp_tables_prefix;
		public static $database_table_name;

		public static $queries;


		/**
		 * @method init().
		 * 
		 * Initializes the queries and table names used through out the class
		 */

		public static function init(){
			global $wpdb;
			self::$wp_tables_prefix = $wpdb->prefix;
			self::$database_table_name = self::$wp_tables_prefix . self::$plugins_tables_prefix . 'tas_analitics';
			self::$queries = array(
				'create' => 'CREATE TABLE IF NOT EXISTS '. self::$database_table_name . ' (id INTEGER PRIMARY KEY AUTO_INCREMENT, assigned_id BIGINT NOT NULL, username CHAR(30) NOT NULL, ipv4_v6_address VARCHAR(45), country CHAR(25), ts TIMESTAMP DEFAULT CURRENT_TIMESTAMP);',
				'drop' => 'DROP TABLE ' . self::$database_table_name . ';',
				'insert' => 'INSERT INTO ' . self::$database_table_name . ' (assigned_id, username, ipv4_v6_address, country) VALUES (%d, \'%s\', \'%s\', \'%s\');',
				'get' => 'SELECT * FROM ' . self::$database_table_name . ' WHERE id = \'%d\'',
				'get-all' => 'SELECT assigned_id, username, ipv4_v6_address, country, ts FROM ' . self::$database_table_name . ';',
				'get-total-count'=> 'SELECT COUNT(*) AS count FROM ' . self::$database_table_name . ';',
				'get-country-counts' => 'SELECT country, COUNT(*) as count FROM ' . self::$database_table_name . ' GROUP BY country;',
				'set-value' => 'INSERT INTO `' . $wpdb->prefix . 'posts` (post_author, post_content, post_type, post_title, post_excerpt, to_ping, pinged, post_content_filtered, post_status) VALUES (%d, \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\');',
				'update-value' => 'UPDATE `' . $wpdb->prefix . 'posts` SET post_content = \'%s\' WHERE post_type= \'%s\'',
				'get-value' => 'SELECT post_content FROM `' . $wpdb->prefix . 'posts` WHERE post_type = \'%s\'',
				'remove-value' => 'DELETE FROM `' . $wpdb->prefix . 'posts` WHERE post_type = \'%s\''
			);
		}

		public static function create_table(){
			global $wpdb;
			$results = $wpdb->query( self::$queries['create'] );
			if(!$result){
				return false;
			}
			return true;
		}

		public static function drop_table(){
			global $wpdb;
			$results = $wpdb->query( self::$queries['drop']);
			if(!$results){
				return false;
			}
			return true;
		}

		/**
		 * @method insert().
		 *
		 * @param int $user_id User id of new Guest.
		 * @param string $username Username Assigned to new Guest.
		 * @param string $ipv4_v6_address IP_V4_V6 Address of new Guest.
		 * @param string $country Country of new Guest.
		 * @return bool If the query executes returns true, on error false.
		 */

		public static function insert($user_id = 0, $username = 'guest', $ipv4_v6_address = '0.0.0.0', $country=''){
			if(!$user_id){
				$user_id = get_current_user_id(); 
			}

			$formated = sprintf( self::$queries['insert'], $user_id, $username, $ipv4_v6_address, $country);
			global $wpdb;
			$results = $wpdb->query( $formated );
			if(!$results){
				return false;
			}
			return true;
		}

		/**
		 * @method get_all().
		 *
		 * @return bool/array If the query executes returns all rows from the plugins table, on error false.
		 */

		public static function get_all(){
			global $wpdb;
			$results = $wpdb->get_results( self::$queries['get-all'] );
			if(!$results){
				return false;
			}
			return $results;
		}

		/**
		 * @method get_total_count().
		 *
		 * @return bool/int If the query executes it returns the number of rows in the table (total guest users), on error false.
		 */

		public static function get_total_count(){
			global $wpdb;
			$results = $wpdb->get_var( self::$queries['get-total-count'] );
			if(!$results){
				return false;
			}
			return intval($results);
		}

		/**
		 * @method get_country_counts().
		 *
		 * @return bool/array If the query executes it returns an array with counts for each available country with it's total users, on error false.
		 */

		public static function get_country_counts(){
			global $wpdb;
			$results = $wpdb->get_results( self::$queries['get-country-counts'] );
			if(!$results){
				return false;
			}
			foreach ($results as $row) {
				$country_counts[$row->country] = $row->count;
			}

			return $country_counts;
		}

		/**
		 * @method set_value().
		 *
		 * @param string $type The post_type we want for value to be stored with in posts.
		 * @param string $value The value we want to store.
		 * @return bool/string If the query executes returns the $value, on error false.
		 */

		public static function set_value( $type, $value ){
			global $wpdb;
			$exists = self::get_value( $type );
			$result;
			if($exists){
				$result = $wpdb->query( sprintf( self::$queries['update-value'], $value, $type) );
			}else{
				$result = $wpdb->query( sprintf( 
					self::$queries['set-value'], 
					get_current_user_id(), 
					$value,   //post_content
					$type,    //post_type
					'',       //post_title,
					'',       //post_excerpt,
					'',       //to_ping,
					'',       //pinged,
					'',       //post_content_filtered,
					'private' //post_status
				) );			
			}
			if(!$result){
				return false;
			}
			return $value;
		}


		/**
		 * @method get_value().
		 *
		 * @param string $type The post_type for the data we want to retrieve.
		 * @return bool/string If the query executes returns the post_content, on error false.
		 */

		public static function get_value( $type ){
			global $wpdb;
			$result = $wpdb->get_results( sprintf( self::$queries["get-value"], $type )  );
			if(!$result){
				return false;
			}
			return $result[0]->post_content;
		}

		/**
		 * @method remove_value().
		 *
		 * @param string $type The post_type for the data we want to remove.
		 * @return bool If the query executes returns true, on error false.
		 */
		
		public static function remove_value( $type ){
			global $wpdb;
			$result = $wpdb->query( sprintf( self::$queries['remove-value'], $type ) );
			if(!$result){
				return false;
			}
			return true;
		}
	}	
}
