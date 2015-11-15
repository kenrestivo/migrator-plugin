<?php

/**
 * Name: Migrate users in bulk as admin
 * Description: Export a user and all their channels from one hub to another
 * Version: 0.1
 * Author: ken restivo <ken@restivo.org>
 */

define ('MIGRATOR_VERSION', 1);

require_once('include/api_auth.php');


function migrator_load(){
	register_hook('construct_page', 'addon/migrator/migrator.php', 'migrator_all_pages');
}

function migrator_unload(){
	unregister_hook('construct_page', 'addon/migrator/migrator.php', 'migrator_all_pages');
}


function migrator_settings(&$a,&$s) {
}

function migrator_settings_post($a,&$post) {
}

function migrator_content(&$a){
	// It's not an API call. Check login.
	if(! is_site_admin()){
		header('HTTP/1.0 401 Unauthorized');
		die('Only admin accounts may use this endpoint.');
	}

	return("Migrator version " . MIGRATOR_VERSION);
}





function export_users(&$a) {

	$r = q("SELECT *  FROM account where account_flags = %d and ((account_roles & %d) < 1)",
	       intval(ACCOUNT_OK),
	       intval(ACCOUNT_ROLE_SYSTEM));

	$ret = array();
	foreach($r as $u){
		$ret[] = $u;
	}

	json_return_and_die(array('status' => 'OK',
				  'users' => $ret));
}




function export_channel_hashes(&$a, $account_id) {


	if( $account_id == ''){
		header('HTTP/1.0 422 Unprocessable Entity');
		die('Must supply account_id parameter.');

	}

	$c = q("select `channel_hash`, `channel_id` from `channel` where `channel_account_id` = %d",
	       intval($account_id));

	if(count($c) < 1){
		header('HTTP/1.0 404 Not Found');
		die('No such account_id '. intval($account_id));
		
	}			

	$ret = array();
	foreach ($c as $r){
		$ret[] = $r;
	}
	json_return_and_die(array('status' => 'OK',
				  'channel_hashes' => $ret));
}




function export_identity(&$a, $channel_hash) {

	if( $channel_hash == ''){
		header('HTTP/1.0 422 Unprocessable Entity');
		die('Must supply channel_hash parameter.');

	}

	require_once('include/identity.php');
		
	$c = q("select channel_id from channel where channel_hash = '%s' LIMIT 1",
	       dbesc($channel_hash));
				
	if(! $c){
		header('HTTP/1.0 404 Not Found');
		die('No such channel '. $channel_hash);
		
	}			
	json_return_and_die(
		identity_basic_export($c[0]['channel_id'],
				      (($_REQUEST['posts']) ? intval($_REQUEST['posts']) : 0 )));
}


function migrator_all_pages(&$a, &$b){
	// TODO: settings and insructions and such
}

function migrator_init(&$a) {

	$x = argc(); 
	if($x > 1){
		api_login($a);
		switch(argv(1)){
		case "version":
			json_return_and_die(array("status" => "OK",
						  "version" => MIGRATOR_VERSION));
			break;
		case "export":
			switch(argv(2)){
			case "users":
				export_users($a);
				break;
			case "channel_hashes":
				export_channel_hashes($a, argv(3));
				break;
			case "identity":
				export_identity($a, argv(3));
				break;
			}
			break;
		case "import":
			break;
		default:
			header('HTTP/1.0 404 Not Found');
			die('No such endpoint');
		}

	} 
}

function migrator_module() { return; }
