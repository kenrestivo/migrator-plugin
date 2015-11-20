<?php

require_once('migrator_utils.php');
require_once('mod/import.php');

function migrator_import_account(&$a) {


	$account = json_decode(file_get_contents('php://input'), TRUE );


	$found_id = get_account_by_email($account['account_email']);
	if($found_id){
		json_return_and_die(array("status" => 'OK',
					  'old_account_id' => $account['account_id'],
					  'already_present?' => true,
					  'account_id' => $found_id));
	}

	$r = q("INSERT INTO account 
			( account_salt,  account_password, account_email,  
                          account_language, account_created, account_flags, account_roles,
                          account_expires, account_service_class )
		VALUES ('%s', '%s', '%s', '%s', '%s', %d, %d, '%s', '%s' )",
	       dbesc($account['account_salt']),
	       dbesc($account['account_password']),
	       dbesc($account['account_email']),
	       dbesc($account['account_language']),
	       dbesc($account['account_created']),
	       dbesc($account['account_flags']),
	       dbesc($account['account_roles']),
	       dbesc($account['account_expires']),
	       dbesc($account['account_service_class'])
		);

	if(! $r) {
		logger('import_account: DB INSERT failed.');
		json_error_die('520 Unknown Error', 
			       t('Server failed to create account'));
	}

	// double check, and fetch the idc
	$new_id = get_account_by_email($account['account_email']);
	if($new_id){
		json_return_and_die(array("status" => 'OK',
					  'old_account_id' => $account['account_id'],
					  'already_present?' => false,
					  'account_id' => $new_id));
	} else {
		logger('import_account: could not retrieve newly created account');
		json_error_die('520 Unknown Error', 
			       t('Server failed to find  account'));
	}

	logger('import_account: ERROR unreachable branch.');
}


function migrator_import_identity(&$a, $email) {
	$found_id = get_account_by_email($email);
	if(! $found_id){
		json_error_die('404 Not Found',
			       'No such account '. $email);
	}
	$res = import_account($a, $found_id);
	json_return_and_die(array("status" => 'OK',
				  'result' => $res,
				  'files' => $_FILES,
				  'email' => $email));
	
}

function migrator_import_items(&$a, $channel_hash) {

	$channel_id  = get_channel_id($channel_hash);
	if(! $channel_id){
		json_error_die('404 Not Found',
			       'No such channel '. $channel_hash);
	}

	
	$src      = $_FILES['filename']['tmp_name'];

	if(intval($_FILES['filename']['size']) < 1){
		json_error_die('422 - Unprocessable Entity',
			       'No file attached, or zero size file');
	}
	$data = @file_get_contents($src);
	unlink($src);


	$res = import_items($channel_hash,$data['item']);


	json_return_and_die(array("status" => 'OK',
				  'result' => $res,
				  'files' => $_FILES,
				  'channel_id' => $channel_id));

}

function migrator_update_directory(&$a, $channel_hash){
	$channel_id  = get_channel_id($channel_hash);
	if(! $channel_id){
		json_error_die('404 Not Found',
			       'No such channel '. $channel_hash);
	}

	proc_run('php','include/notifier.php','location',$channel_id);

	proc_run('php', 'include/directory.php', $channel_id);

	json_return_and_die(array("status" => 'OK',
				  'channel_hash' => $channel_hash,
				  'channel_id' => $channel_id));

}