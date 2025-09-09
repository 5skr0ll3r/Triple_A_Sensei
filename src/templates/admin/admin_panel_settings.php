<?php

//include TRIPLE_A_SENSEI_PATH . 'includes/db-api.php';

if(isset($_POST['site-key']) && !empty($_POST['site-key'])){
	Triple_A_Sensei_DB_API::set_value("cap-site-key", $_POST['site-key']);
}
if(isset($_POST['secret-key']) && !empty($_POST['secret-key'])){
	Triple_A_Sensei_DB_API::set_value("cap-secret-key", $_POST['secret-key']);
}

$site_key = Triple_A_Sensei_DB_API::get_value("cap-site-key");
$secret_key = Triple_A_Sensei_DB_API::get_value("cap-secret-key");
?>

<form method="POST">
	<label for="">ReCaptcha Site Key</label>
	<input type="text" name="site-key" placeholder="<?= $site_key? $site_key : "Site Key"  ?>">
	<label for="">ReCaptcha Secret Key</label>
	<input type="text" name="secret-key" placeholder="<?= $secret_key? $secret_key : "Secret Key" ?>">
	<input type="submit" name="submit-keys">
</form>
