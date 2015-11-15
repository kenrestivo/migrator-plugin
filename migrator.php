<?php

/**
 * Name: Migrate users in bulk as admin
 * Description: Export a user and all their channels from one hub to another
 * Version: 0.1
 * Author: ken restivo <ken@restivo.org>
 */

define ('MIGRATOR_VERSION', 2);

require_once('include/api_auth.php');
require_once('include/identity.php');
require_once('include/items.php');


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

function json_error_die($code, $message){
	header('HTTP/1.0 ' . $code);
	json_return_and_die(array("status" => "Error",
				  "message" => $message));
}


function migrator_content(&$a){
	// It's not an API call. Check login.
	if(! is_site_admin()){
		json_error_die('401 Unauthorized', 
			       'Only admin accounts may use this endpoint.');
	}

	return("Migrator version " . MIGRATOR_VERSION);
}



/// via https://stackoverflow.com/questions/2012187/how-to-check-that-a-string-is-an-int-but-not-a-double-etc
function validatesAsInt($number)
{
	$number = filter_var($number, FILTER_VALIDATE_INT);
	return ($number !== FALSE);
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


	if( $account_id == '' ){
		json_error_die('422 Unprocessable Entity',
			       'Must supply account_id parameter.');

	}

	if(! validatesAsInt($account_id)){
		json_error_die('422 Unprocessable Entity',
			       "That's not a number: ". $account_id);
	}

	$c = q("select `channel_hash`, `channel_id` from `channel` where `channel_account_id` = %d",
	       intval($account_id));

	if(count($c) < 1){
		json_error_die('404 Not Found',
			       'No channels for '. $account_id);
	}			

	$ret = array();
	foreach ($c as $r){
		$ret[] = $r;
	}
	json_return_and_die(array('status' => 'OK',
				  'channel_hashes' => $ret));
}


function get_channel_id($channel_hash){
	$c = q("select channel_id from channel where channel_hash = '%s' LIMIT 1",
	       dbesc($channel_hash));
				
	if(! $c){
		json_error_die('404 Not Found',
			       'No such channel '. $channel_hash);
	}	
	return $c[0]['channel_id'];
}

function export_identity(&$a, $channel_hash) {

	if( $channel_hash == ''){
		json_error_die('422 Unprocessable Entity',
			       'Must supply channel_hash parameter.');

	}
		
	json_return_and_die(
		identity_basic_export(get_channel_id($channel_hash),
				      (($_REQUEST['posts']) ? intval($_REQUEST['posts']) : 0 )));
}


function migrator_all_pages(&$a, &$b){
	// TODO: settings and insructions and such
}


function export_items(&$a, $channel_hash, $year, $month){

	if(validatesAsInt($year) && validatesAsInt($month)){
	} else {
		json_error_die('422 Unprocessable Entity',
			       'Month and year must be numbers'. $month . " " . $year);
	}

	if(($month < 1) || ($month > 12)){
		json_error_die('422 Unprocessable Entity',
			       'Invalid month'. $month);
	}
	
	if(($year < 1) || ($year > date('Y'))){
		json_error_die('422 Unprocessable Entity',
			       'Are you from the future? Invalid year '. $year);
	}

	$items = identity_export_year(get_channel_id($channel_hash), $year, $month);
	if(count($items) < 1){
		json_error_die('404 Not Found',
			       "No posts for $channel_hash on $year-$month");
	}
	json_return_and_die($items);
}


function first_post(&$a, $channel_hash){
	if( $channel_hash == ''){
		json_error_die('422 Unprocessable Entity',
			       'Must supply channel_hash parameter.');

	}
	$first = first_post_date(get_channel_id($channel_hash));
	
	if(! $first){
		json_error_die('404 Not Found',
			       "No posts for " . $channel_hash);
	}
	json_return_and_die(array("status" => "OK",
				  "date" => $first));
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
			case "first_post":
				first_post($a, argv(3));
				break;
			case "items":
				export_items($a, argv(3), argv(4), argv(5));
				break;
			}
			break;
		case "import":
			break;
		default:
			json_error_die('404 Not Found',
				       'No such endpoint');
		}

	} 
}

function migrator_module() { return; }
