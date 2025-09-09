<?php

if( !defined('ABSPATH') ){
	exit;
}

function triple_a_export_handler() {
	if (!isset($_POST['_triple-a_nonce']) || !wp_verify_nonce($_POST['_triple-a_nonce'], 'export')) {
		wp_die('Invalid nonce');
	}
	if (!current_user_can('manage_options')) {
		wp_die('Insufficient permissions');
	}
	
	include_once TRIPLE_A_SENSEI_PATH . 'includes/db-api.php';
	$all_data = Triple_A_Sensei_DB_API::get_all();

	if(!$all_data){
		wp_die("No data to export"); 
	}

	$rows = [];
	// Parsing all guest users meta to find which ones enrolled and in what courses
	foreach ($all_data as $guest) {
		$user_meta = get_user_meta($guest->assigned_id);
		$enrolments = json_decode($user_meta['wp9r_risksensei_enrolment_providers_state'][0] ?? '[]', true);
		$enrolled_courses = [];
		foreach ($enrolments as $course_id => $details) {
			if (!empty($details['manual']['enrolment_status'])) {
				$parts = explode(' ', get_the_title($course_id));
				$course_num = $parts[1] ?? $parts[0];
				$enrolled_courses[] = $course_num;
			}
		}

		$rows[] = [
			"ID" => $guest->assigned_id,
			"Username" => $guest->username,
			"IP" => $guest->ipv4_v6_address,
			"Country" => $guest->country,
			"Enrolled" => implode('-', array_unique($enrolled_courses)),
			"Registered" => $guest->ts
		];
	}
	
	$filename = 'triple_a_sensei_export_data-' . date('Ymd') . '.xlsx';

	header('Content-Type: text/csv; charset=utf-8');
	header("Content-Disposition: attachment; filename={$filename}");
	header('Cache-Control: max-age=0');

	// Writing to the stream for download
	$out = fopen("php://output", 'w');
	if (!empty($rows)) {
		fputcsv($out, array_keys($rows[0]));
	}
	foreach ($rows as $row) {
		fputcsv($out, $row);
	}
	fclose($out);
	exit;
}