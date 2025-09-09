<?php

if (!defined('ABSPATH')) {
	exit;
}

if(!is_user_logged_in()){
	exit;
}

if(!current_user_can('manage_options')){
	exit;
}

$all_data = array();
$total_guests = 0;
$country_counts = array();
$option = 0;
$error = '';


if(isset($_GET['option']) && filter_var($_GET['option'], FILTER_VALIDATE_INT)){
	$option = intval($_GET['option']);
}

include_once TRIPLE_A_SENSEI_PATH . 'includes/db-api.php';
if($option === 0){
	$all_data = Triple_A_Sensei_DB_API::get_all();
	$total_guests = Triple_A_Sensei_DB_API::get_total_count();
}
else{
	$country_counts = Triple_A_Sensei_DB_API::get_country_counts();
}


global $wp;
$current_url = home_url( $wp->request );
?>
<div class="tripple-sensei-admin-panel">
	<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
	<div class="tripple-sensei-admin-header">
		
	</div>
	<div class="tripple-sensei-admin-menu">
		<a href="<?= esc_url( add_query_arg( ['page' => 'tas_slug', 'option' => 0], admin_url('admin.php') ) ); ?>">User data</a>
		<a href="<?= esc_url( add_query_arg( ['page' => 'tas_slug', 'option' => 1], admin_url('admin.php') ) ); ?>">Analytics</a>
		<a href="<?= esc_url( add_query_arg( ['page' => 'tas_slug', 'option' => 2], admin_url('admin.php') ) ); ?>">Export</a>
		<a href="<?= esc_url( add_query_arg( ['page' => 'tas_slug', 'option' => 3], admin_url('admin.php') ) ); ?>">Settings</a>
	</div>
	<div class="tripple-sensei-admin-content"> <?php
		if($option === 1){
			include_once TRIPLE_A_SENSEI_PATH . 'templates/admin/admin_panel_charts.php';
		}
		elseif($option === 2){
				include_once TRIPLE_A_SENSEI_PATH . 'templates/admin/admin_panel_export.php';
		}
		elseif($option === 3){
				include_once TRIPLE_A_SENSEI_PATH . 'templates/admin/admin_panel_settings.php';
		}
		else{
			include_once TRIPLE_A_SENSEI_PATH . 'templates/admin/admin_panel_entries.php';
		} ?>
	</div>
	<div class="tripple-sensei-admin-footer"><?php
		if(isset($error)){?>
			<h2><?=$error?></h2> <?php
		}
	?></div>
</div>
<style>
	.tripple-sensei-admin-panel{
		text-align: center;
	}

	.tripple-sensei-admin-menu{
		display: flex;
		flex-direction: row;
		gap: 10px;
		justify-content: center;
		align-items: center;
	}
</style>
